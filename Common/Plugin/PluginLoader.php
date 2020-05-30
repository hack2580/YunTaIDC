<?php

namespace YunTaIDC\Plugin;

use YunTaIDC\Database\Database;

class PluginLoader{
    
    public $PluginFilePath;
    
    public function __construct(){
        $this->PluginFilePath = ROOT.'/plugins/';
    }
    
    public function LoadPlugin($plugin){
        $InstallerFile = $this->PluginFilePath . '/' . $plugin . '/installer.php';
        if(file_exists($InstallerFile)){
            throw new Exception("云塔提醒你：插件安装器尚未删除！可能是未安装！若已安装请手动删除安装器！");
            exit;
        }
        $DB = new Database();
        $plugindata = $DB->get_row("SELECT * FROM `ytidc_plugin` WHERE `name`='{$plugin}'");
        $pluginconfig = json_decode($plugindata['config'], true);
        $PluginFile = $this->PluginFilePath . '/' . $plugin . '/' . $plugin . '.php';
        require_once($PluginFile);
        $pluginclass = new $plugin();
        if(method_exists($pluginclass, 'onLoad')){
            $pluginclass->onLoad($pluginconfig);
        }
        return $pluginclass;
    }
    
    public function LoadInstaller($plugin){
        $InstallerFile = $this->PluginFilePath . '/' . $plugin . '/installer.php';
        if(!file_exists($InstallerFile)){
            throw new Exception("云塔提醒你：插件安装器不存在，请不要重复安装！");
            exit;
        }
        require_once($InstallerFile);
        $installer = $plugin();
        if(method_exists($installer, 'Install')){
            if($installer->Install() == true){
                throw new Exception("云塔提醒您：插件安装失败，返回信息" . $installer->error_msg);
                exit;
            }
        }
        if(unlink($InstallerFile)){
            return true;
        }else{
            throw new Exception("云塔提醒您：插件安装成功，删除安装器失败，请手动删除避免不必要的损失！");
            exit;
        }
    }
    
}

?>