<?php


namespace app\common\service;

use app\common\model\User AS UserModel;
use app\common\model\UserEvent;
use app\common\model\ActivityEvent;
use app\common\model\Activity AS ActivityModel;
use think\Cache;


class Activity
{
    /**
     * 任务中心列表
     * @param UserModel $user
     * @return array
     */
    public function groupListInfo(UserModel $user) {
        $actRows = M('activity')->where('publish_at', '>', 0)->order('publish_at DESC')->select();
        $groups = [];
        $items = [];

        if (!empty($actRows)) {
            foreach ($actRows as $row) {
                if ($row->show_type == 1) {
                    $groups[] = $this->groupInfo($row, $user);
                } else {
                    $items[] = $this->simpleInfo($row, $user);
                }
            }
        }
        return [
            'groups' => $groups,
            'items' => $items
        ];
    }

    /**
     * 按组整理任务信息
     * @param ActivityModel $row
     * @param UserModel $user
     * @return mixed
     */
    public function groupInfo(ActivityModel $row, UserModel $user) {
        $data['name'] = $row->subject;
        $data['note'] = $row->intro;
        // $data['sum_amount'] = 0;
        $data['sum_amount'] = 8800;
        $data['sum_point'] = 0;
        if ($row->key === 'G-001') { // 邀请好友
            $data['referrer_success_num'] = S('user')->realReferrerNum($user);
        }
        $data['items'] = [];

        if (!empty($row->events)) {
            $has_detail = true;
            foreach ($row->events as $event) {
                //$data['sum_amount'] += $event->amount;
                $data['sum_point'] += $event->point;

                $userEvent = M('userEvent')->findByUserIDAndHash($user->id, $event->hash); // 可能是null
                $cacheKey = $userEvent ? 'Activity:GroupItem:' . $userEvent->id : false;
                $item = $cacheKey ? Cache::get($cacheKey) : null;
                if (!$item) {
                    $item =  $this->simpleEventInfo($event);
                    $item['is_finished'] = $this->overRepeatTimes($user, $event) ? 1 : 0;
                    $item['withdraw_status'] = $this->withdrawStatus($event, $userEvent);
                    $item['amount_record_no'] = '';
                    if ($item['is_finished'] && $item['withdraw_status'] == 0) {
                        if ($userEvent && !$userEvent->collect_at) {
                            $item['amount_record_no'] = $userEvent->record_no;
                        }
                    }

                    $_tuple = ['start', 'next', 'amount'];
                    $detail = null;
                    $share_pic = completeUrl('/static/images/share_rq.jpg');
                    $share_desc = '我刚刚领取了88元红包，快来领取红包秒提现';
                    $referrer_success_num = $data['referrer_success_num'];
                    switch ($event->hash) {
                        case 'Invite-G-1':
                            if (!$item['is_finished']) { // Invite-G-1 为前提任务
                                $has_detail = false;
                                $detail = 1;
                            }
                            break;
                        case 'Invite-F-1':
                            $_tuple = [0, 10, 300];
                            $share_pic = completeUrl('/static/images/share_rq_3.jpg');
                            $share_desc = '我刚领取了3元红包，点击即可领取最高88元红包~';
                            break;
                        case 'Invite-F-2':
                            $_tuple = [10, 40, 900];
                            $share_pic = completeUrl('/static/images/share_rq_9.jpg');
                            $share_desc = '我刚领取了9元红包，点击即可领取最高88元红包~';
                            break;
                        case 'Invite-F-3':
                            $_tuple = [40, 140, 3000];
                            $share_pic = completeUrl('/static/images/share_rq_30.jpg');
                            $share_desc = '我刚领取了30元红包，点击即可领取最高88元红包~';
                            break;
                        case 'Invite-F-4':
                            $_tuple = [140, 290, 4500];
                            $share_pic = completeUrl('/static/images/share_rq_45.jpg');
                            $share_desc = '我刚领取了45元红包，点击即可领取最高88元红包~';
                            break;
                    }
                    if ($has_detail && $_tuple[0] !== 'start') {
                        if ($referrer_success_num >= $_tuple[0] && $referrer_success_num < $_tuple[1]) {
                            $row_success_num = $referrer_success_num - $_tuple[0];
                            $row_unit_amount = $_tuple[2] / ($_tuple[1] - $_tuple[0]);
                            $detail = [
                                'cur_amount' => $row_success_num * $row_unit_amount,
                                'next_need' => $_tuple[1] - $referrer_success_num
                            ];
                        }
                    }
                    $item['detail'] = $detail;
                    $item['share'] = [
                        'pic' => $share_pic,
                        'desc' => $share_desc
                    ];
                    if ($cacheKey && $item['withdraw_status'] == 2) {
                        Cache::set($cacheKey, $item, 1800);
                    }
                }
                $data['items'][] = $item;
            }
        }
        return $data;
    }

    /**
     * 任务信息
     * @param ActivityModel $activity
     * @param UserModel $user
     * @return mixed
     */
    public function simpleInfo(ActivityModel $activity, UserModel $user) {
        $data['key'] = $activity->key;
        $data['title'] = $activity->subject;
        $data['note'] = $activity->intro;
        $data['amount'] = 0;
        $data['point'] = 0;
        $data['is_finished'] = 0;

        if ($activity->key === 'T-008') { // 开宝箱
            $data['point'] = M('activityEvent')->where('activity_id', 9)->cache(3600)->max('point');
            $last_time = M('userEvent')->where('user_id', $user->id)
                ->where('activity_id', 9)->where('collect_at', '>', 0)->order('collect_at DESC')->value('collect_at');
            $remaining_time = 0;
            if ($last_time) {
                $remaining_time = 7200 - ( time() - $last_time );
                if ( $remaining_time > 0 ) {
                    $data['is_finished'] = 1;
                }
            }
            $remaining_time > 0 || $remaining_time = 0;
            $data['remaining_time'] = $remaining_time;
        } elseif (!empty($activity->events)) {
            foreach ($activity->events as $event) {
                $data['amount'] = $data['amount'] ? min($data['amount'], $event->amount) : $event->amount;
                $data['point'] = $data['point'] ? min($data['point'], $event->point) : $event->point;
                if ($this->overRepeatTimes($user, $event)) {
                    $data['is_finished'] = 1;
                }
            }
        }

        return $data;
    }

    /**
     * 事件信息
     * @param ActivityEvent $event
     * @return array
     */
    public function simpleEventInfo(ActivityEvent $event) {
        return [
            'key' => $event->hash,
            'title' => $event->getAttr('name'),
            'amount' => $event->amount,
            'point' => $event->point,
            'auto_withdraw' => $event->auto_withdraw
        ];
    }

    /**
     * 检查是否超出重复触发限制
     * @param UserModel $user
     * @param ActivityEvent $event
     * @return boolean
     */
    public function overRepeatTimes(UserModel $user, ActivityEvent $event) {
        if($event->repeat_value === -1) {
            return true;
        }

        $model = M('userEvent');
        $model->where('user_id', $user->id)
            ->where('activity_id', $event->activity_id)
            ->where('event_hash', $event->hash);

        switch ($event->getData('repeat_type')) {
            case 1:// 按次数
                $count = $model->count();
                break;
            case 2: // 每日次数
                $start = strtotime(date('Y-m-d') );
                $end = $start + 86400;

                $model->whereBetween('create_time', [$start, $end]);

                $count = $model->count();
                break;

            default: // 不限
                $count = 0;
        }
        return $count >= $event->repeat_value;
    }

    /**
     * 任务提现状态
     * @param ActivityEvent $event
     * @param UserEvent $userEvent
     * @return int
     */
    public function withdrawStatus(ActivityEvent $event, $userEvent) {
        $status = 0;
        if ($event->amount > 0) {
            if ($userEvent) {
                if ($userEvent->collect_at > 0) {
                    $status = 1; // 申请中
                }
                if ($userEvent->withdraw_id > 0) {
                    $withdraw = M('withdraw')->findById($userEvent->withdraw_id);
                    if ($withdraw && $withdraw->transfer_status == 1) {
                        $status = 2; // 已提现
                    }
                }
            }
        } else {
            $status = 2; // 已完成
        }
        return $status;
    }

    /**
     * user call event
     * @param UserModel $user
     * @param ActivityEvent $event
     * @return null|UserEvent
     */
    public function call(UserModel $user, ActivityEvent $event) {
        if( !$this->overRepeatTimes($user, $event) ) {
            $activity = $event->activity;
            if($activity) {
                return $this->createUserEvent($user, $event);
            }
        }
        return null;
    }

    /**
     * 记录用户事件
     * @param UserModel $user
     * @param ActivityEvent $event
     * @return UserEvent
     */
    public function createUserEvent(UserModel $user, ActivityEvent $event) {
        $userEvent = M('userEvent')->getInstance();
        $userEvent->user_id = $user->id;
        $userEvent->event_hash = $event->hash;
        $userEvent->activity_id = $event->activity_id;
        $userEvent->point = $event->point;
        $userEvent->amount = $event->amount;
        $userEvent->save();
        return $userEvent;
    }
}