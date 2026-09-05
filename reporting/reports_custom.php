<?php
/**
 * ksf_FA_Rep_Operations - Reports Customization
 *
 * Registers all operations reporting suite reports:
 * - Inventory History
 * - Pick List
 * - Price Change Since
 * - Bank Daily Sweep
 * - Tax Cash Basis
 */

global $reports, $dim;

$reports->addReportClass(_('Inventory'), RC_INVENTORY);
$reports->addReportClass(_('Banking'), RC_BANKING);

$reports->addReport(RC_INVENTORY, '_inventory_history', _('Inventory History'),
    array(
        _('Location') => 'LOCATIONS',
        _('Category') => 'CATEGORIES',
        _('Date From') => 'DATEBEGIN',
        _('Date To') => 'DATEEND',
        _('Comments') => 'TEXTBOX',
        _('Destination') => 'DESTINATION'));

$reports->addReport(RC_INVENTORY, '_picklist', _('Pick List'),
    array(
        _('Location') => 'LOCATIONS',
        _('Customer') => 'CUSTOMERS_NO_FILTER',
        _('Item Selection') => 'TEXT',
        _('Comments') => 'TEXTBOX',
        _('Destination') => 'DESTINATION'));

$reports->addReport(RC_INVENTORY, '_price_change_since', _('Price Changes Since'),
    array(
        _('Date') => 'DATE',
        _('Category') => 'CATEGORIES',
        _('Location') => 'LOCATIONS',
        _('Comments') => 'TEXTBOX',
        _('Destination') => 'DESTINATION'));

$reports->addReport(RC_BANKING, '_bank_daily_sweep', _('Bank Daily Sweep'),
    array(
        _('Date') => 'DATE',
        _('Bank Account') => 'BANK_ACCOUNTS',
        _('Comments') => 'TEXTBOX',
        _('Destination') => 'DESTINATION'));

$reports->addReport(RC_BANKING, '_tax_details_cash', _('Tax Details (Cash Basis)'),
    array(
        _('Date From') => 'DATEBEGIN',
        _('Date To') => 'DATEEND',
        _('Tax Type') => 'TAX_TYPES',
        _('Summary Only') => 'YES_NO',
        _('Comments') => 'TEXTBOX',
        _('Destination') => 'DESTINATION'));
