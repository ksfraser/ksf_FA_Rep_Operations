<?php
/**********************************************************************
    Copyright (C) FrontAccounting, LLC.
	Released under the terms of the GNU General Public License, GPL, 
	as published by the Free Software Foundation, either version 3 
	of the License, or (at your option) any later version.
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
    See the License here <http://www.gnu.org/licenses/gpl-3.0.html>.
***********************************************************************/
 = 'SA_KSF_REPOPERATIONS';
// ----------------------------------------------------------------
// $ Revision:	1.0 $
// Creator:	Kevin Fraser
// date_:	2024-08-16
// Title:	Item Price Changed Since A Date
// ----------------------------------------------------------------
/**
*	This report depends upon having changed the _prices table
*	to include a last_updated column.
*
*	ASSUMPTION:
*		We only want to look at RETAIL price changes.
*
*	TODO:
*		convert function names away from the ones sourced from
*			_dated_stock.  
*		Add the ability to select other price books
*
**********************************************************************/
$path_to_root="..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/gl/includes/gl_db.inc");
include_once($path_to_root . "/inventory/includes/inventory_db.inc");
include_once($path_to_root . "/includes/db/manufacturing_db.inc");

//----------------------------------------------------------------------------------------------------

print_changed_prices();

function getUpdatedPrices($category, $location, $rep_date, $sales_type_id = 1)
{
/**
*		SELECT 1_stock_master.stock_id,
*		                        1_stock_master.description,
*		                        1_prices.price,
*		                        IF(1_stock_moves.stock_id IS NULL, '', 1_stock_moves.loc_code) AS loc_code,
*		                        SUM(IF(1_stock_moves.stock_id IS NULL,0,1_stock_moves.qty)) AS QtyOnHand
*		                FROM (1_stock_master,
*		                        1_prices,
*		                     1_stock_moves,
*		                     1_stock_category)
*		                WHERE 1_stock_master.category_id=1_stock_category.category_id
*		                 AND 1_stock_moves.stock_id=1_stock_master.stock_id
*		                 AND 1_prices.stock_id=1_stock_master.stock_id
*		                 AND 1_prices.sales_type_id=1
*		                 AND 1_prices.last_updated >= '2024-01-01'
*		                 group by 1_prices.stock_id, 1_stock_moves.loc_code
*		         ORDER BY 1_stock_master.category_id,
*		                1_stock_master.stock_id
*/
	$sql = "SELECT "
			.TB_PREF."stock_master.stock_id,"
			.TB_PREF."stock_master.description,"
			.TB_PREF."prices.price,
			IF(".TB_PREF."stock_moves.stock_id IS NULL, '', ".TB_PREF."stock_moves.loc_code) AS loc_code,
			SUM(IF(".TB_PREF."stock_moves.stock_id IS NULL,0,".TB_PREF."stock_moves.qty)) AS QtyOnHand,"
			.TB_PREF."stock_category.description AS cat_description
		FROM ("
			.TB_PREF."stock_master,"
			.TB_PREF."stock_moves,"
			.TB_PREF."stock_category,"
			.TB_PREF."prices
			)
		WHERE "
			.TB_PREF."stock_master.category_id=".TB_PREF."stock_category.category_id";
		$sql .= " AND ".TB_PREF."stock_master.stock_id = ".TB_PREF."stock_moves.stock_id";
		$sql .= " AND ".TB_PREF."stock_master.stock_id = ".TB_PREF."prices.stock_id";
		$sql .= " AND ".TB_PREF."prices.sales_type_id =" . $sales_type_id;
	if ($category != 0)
		$sql .= " AND ".TB_PREF."stock_master.category_id = ".db_escape($category);
	if ($rep_date != 0)
		$sql .= " AND ".TB_PREF."prices.last_updated >= '$rep_date'";		
	if ($location != 'all')
		$sql .= " AND IF(".TB_PREF."stock_moves.stock_id IS NULL, '1=1',".TB_PREF."stock_moves.loc_code = ".db_escape($location).")";
	$sql .= " GROUP BY "
			.TB_PREF."prices.stock_id,"
			.TB_PREF."stock_moves.loc_code";
	$sql .= " ORDER BY "
			.TB_PREF."stock_master.category_id,"
			.TB_PREF."stock_master.stock_id";

//display_notification( print_r( $sql, true ) );

    return db_query($sql,"No transactions were returned");
}

//----------------------------------------------------------------------------------------------------

function print_changed_prices()
{
    global $comp_path, $path_to_root, $pic_height;

	$rep_date = $_POST['PARAM_0'];
    $category = $_POST['PARAM_1'];
    $location = $_POST['PARAM_2'];
    $comments = $_POST['PARAM_3'];
	$destination = $_POST['PARAM_4'];

	//$sales_type_id = $_POST['PARAM_5'];
	$sales_type_id = 1;

	if ($destination)
		include_once($path_to_root . "/reporting/includes/excel_report.inc");
	else
		include_once($path_to_root . "/reporting/includes/pdf_report.inc");

	if ($category == ALL_NUMERIC)
		$category = 0;
	if ($category == 0)
		$cat = _('All');
	else
		$cat = get_category_name($category);

	if ($location == ALL_TEXT)
		$location = 'all';
	if ($location == 'all')
		$loc = _('All');
	else
		$loc = get_location_name($location);
		
	$cols = array(0, 100, 250, 359, 450, 550);
	$headers = array(_('Stock ID'), _('Description'), _('Price '), _('  Location '), _('Quantity'));
	$aligns = array('left',	'left',	'right', 'right', 'right');


    $params =   array( 	0 => $comments,
    			1 => array('text' => _('Category'), 'from' => $cat, 'to' => ''),
    			2 => array('text' => _('Location'), 'from' => $loc, 'to' => ''),
			3 => array('text' => _('Since Date'), 'from' => $rep_date, 'to' => '')
    		);

	$user_comp = "";

	$res = getUpdatedPrices($category, $location, date2sql($rep_date), $sales_type_id );

    $rep = new FrontReport(_('Prices Changed Since') . $rep_date, "PricesChangesSince", user_pagesize());

    $rep->Font();
    $rep->Info($params, $cols, $headers, $aligns);
    $rep->NewPage();

	$catt = '';
	while ($trans=db_fetch($res))
	{
		if ($location == 'all')
			$loc_code = "";
		else
			$loc_code = $location;

		if ($catt != $trans['cat_description'])
		{
			if ($catt != '')
			{
				$rep->Line($rep->row - 2);
				$rep->NewLine(2, 3);
			}
			if( isset( $trans['category_id'] ) )
				$rep->TextCol(0, 1, $trans['category_id']);
			$rep->TextCol(1, 2, $trans['cat_description']);
			$catt = $trans['cat_description'];
			$rep->NewLine();
		}
		$rep->NewLine();
		$dec = get_qty_dec($trans['stock_id']);
/**
       function TextWrap($xpos, $ypos, $len, $str, $align = 'left', $border = 0, $fill = 0, $link = NULL, $stretch = 1, $spacebreak=false)
        function TextCol($c, $n, $txt, $corr=0, $r=0, $border=0, $fill=0, $link=NULL, $stretch=1)
        {
                return $this->TextWrap($this->cols[$c], $this->row - $r, $this->cols[$n] - $this->cols[$c] + $corr, $txt, $this->aligns[$c], $border, $fill, $link, $stretch);
        }

*/
		//$rep->TextCol(0, 1, $trans['stock_id'], 0, 0, 0, "../../../inventory/manage/items.php?stock_id=" . $trans['stock_id'], 1);
		$rep->TextCol(0, 1, $trans['stock_id'], 0, 0, 0, null, 1);
		$rep->TextCol(1, 2, $trans['description']);
		$rep->TextCol(2, 3, $trans['price']);
		$rep->TextCol(3, 4, $trans['loc_code']);
		$rep->AmountCol(4, 5, $trans['QtyOnHand'], $dec);
		
	}
	$rep->Line($rep->row - 4);
	$rep->NewLine();
    $rep->End();
}

?>
