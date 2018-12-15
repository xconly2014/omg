<?php
namespace app\api\service\other;

use app\api\service\AbstractApiHandler;

class Config extends AbstractApiHandler
{
    public $rule = [];

    public function api() {
        $data = $this->configList();
        if (isset($data['qrcode'])) {
            $data['qrcode'] = completeUrl($data['qrcode']);
        }
        return $data;
    }

    protected function configList() {
        $rows = M('config')->cache()->select();
        $list = [];
        if (!empty($rows)) {
            foreach ($rows as $row) {
                if (stripos($row->name, 'pic_') === 0) {
                    $list[ $row->name ] = completeUrl($row->getAttr('value'));
                } elseif ($row->name === 'qrcode') {
                    $list[ $row->name ] = completeUrl($row->getAttr('value'));
                } else {
                    $list[ $row->name ] = $row->getAttr('value');
                }
            }
        }
        if (isset($list['is_audit_during']) && $list['is_audit_during'] == 1) {
            if (isset($list['qrcode']) && isset($list['qrcode2'])) {
                $list['qrcode'] = $list['qrcode2'];
            }
        }
        if (isset($list['qrcode2'])) unset($list['qrcode2']);

        return $list;
    }
}