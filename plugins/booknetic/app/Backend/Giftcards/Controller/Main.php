<?php

namespace BookneticApp\Backend\Giftcards\Controller;

use BookneticApp\Backend\Giftcards\Model\Giftcard;
use BookneticApp\Providers\Controller;
use BookneticApp\Providers\DataTable;
use BookneticApp\Providers\DB;
use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Permission;


class Main extends Controller
{
    public function index()
    {
		$dataTable = new DataTable("
			SELECT tb1.*,
			(SELECT SUM(giftcard_amount) FROM `" . DB::table('appointment_customers') . "` WHERE giftcard_id=tb1.id) AS `spent`
		 FROM `" . DB::table('giftcards') . "`  tb1
		");

		$dataTable->setTableName('giftcards');
		$dataTable->setTitle(bkntc__('Giftcards'));
		$dataTable->addNewBtn(bkntc__('ADD GIFTCARD'));

		$dataTable->deleteFn( [static::class , '_delete'] );

		$dataTable->searchBy(["code"]);

		$dataTable->addColumns(bkntc__('№'), DataTable::ROW_INDEX);
		$dataTable->addColumns(bkntc__('CODE'), function( $gift ){

			return $gift['code'];
		}, ['attr' => ['column' => 'code']]);

		$dataTable->addColumns(bkntc__('BALANCE'), function( $gift ){
			return Helper::price( $gift[ 'amount' ] );
		}, ['attr' => ['column' => 'amount']]);

		$dataTable->addColumns(bkntc__('SPENT'), function( $gift ){
			if( is_null($gift['spent']))
			{
				$gift['spent'] = $gift['amount'];
			}
			return Helper::price( $gift['spent'] );
		}, ['attr' => ['column' => 'spent']]);

		$dataTable->addColumns(bkntc__('LEFTOVER'), function( $gift ){
			if( is_null($gift['spent']))
			{
				$leftover = $gift['amount'];
			}

			$leftover = $gift['amount'] - $gift['spent'];

			return Helper::price( $leftover );
		}, ['attr' => ['column' => 'leftover']]);

		$dataTable->addColumns(bkntc__('USAGE HISTORY'), function(){
			return '<img class="invoice-icon" src="' . Helper::icon('invoice.svg') . '">';
		}, ['attr' => ['column' => 'usage_history'], 'is_html' => true,]);

        $table = $dataTable->renderHTML();

        $this->view( 'index', ['table' => $table]);
    }
}