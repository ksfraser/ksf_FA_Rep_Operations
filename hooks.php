<?php
/**
 * ksf_FA_Rep_Operations - Operations Reporting Suite
 *
 * @BABOK Related: FR-OPS-001
 */

define('SS_KSF_FA_REP_OPERATIONS', 148 << 8);

class hooks_ksf_FA_Rep_Operations extends hooks
{
    var $module_name = 'ksf_FA_Rep_Operations';
    var $version = '2.4.19-1.0.0';

    function install_access()
    {
        $security_sections[SS_KSF_FA_REP_OPERATIONS] = _('KSF Operations Reports');

        $security_types['SA_KSF_REPOPERATIONS'] = _('View Operations Reports');

        return array($security_sections, $security_types);
    }

    function activate_extension($check_only = false)
    {
        $updates = array(
            'sql/install.sql' => array(),
        );

        if (!$check_only) {
            $this->register_extension('ksf_FA_Rep_Operations', $updates);
        }

        return $this->verify_updates($updates, $check_only);
    }

    function deactivate_extension($check_only = false)
    {
        return $this->remove_extension($check_only);
    }

    function getModuleConstants(&$data, $opts = array())
    {
        $data['SS_KSF_FA_REP_OPERATIONS'] = SS_KSF_FA_REP_OPERATIONS;
    }
}
