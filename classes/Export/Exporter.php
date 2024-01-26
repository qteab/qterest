<?php


namespace QTEREST\Export;

use QTEREST\Vendor\PhpOffice\PhpSpreadsheet\Spreadsheet;
use QTEREST\Vendor\PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use wpdb;

class Exporter {

	/**
	 * @var Spreadsheet
	 */
	protected $spreadsheet;



	public function __construct() {
		$this->spreadsheet = new Spreadsheet();
	}

	public function export() {
		$formResponses = $this->getFormResponses();
		$columns       = $this->getColumnsFromResponses( $formResponses );

		$data = $this->prepareDataForSpreadSheet( $formResponses, $columns );

		$sheet = $this->spreadsheet->getActiveSheet();

		$sheet->fromArray( $data );

		$filename = 'qterest-export-' . date( 'Y-m-d' ) . '.xlsx';

		$this->setHeaders( $filename );

		$writer = new Xlsx( $this->spreadsheet );
		$writer->save( 'php://output' );

		die();
	}

	/**
	 * @return array
	 */
	private function getFormResponses(): array {
		/** @var $wpdb wpdb */
		global $wpdb;

		$results = $wpdb->get_results(
			"SELECT postmeta.meta_value AS response, posts.post_date AS date
                            FROM {$wpdb->postmeta} postmeta
                            INNER JOIN {$wpdb->posts} posts
                                ON posts.id = postmeta.post_id
                            WHERE posts.post_type = 'contact_requests'
                            AND postmeta.meta_key = 'request_content'"
		);

		$processedResponses = array();
		foreach ( $results as $result ) {	
			$responseData         = unserialize( maybe_unserialize( $result->response ) );
			if ( !is_array($responseData)):
				continue;
			endif;
			$processedResponses[] = array_merge(
				array(
					'date' => $result->date,
				),
				$responseData
			);
		}

		return $processedResponses;
	}

	/**
	 * @param array $responses
	 * @return array
	 */
	private function getColumnsFromResponses( array $responses ): array {
		$columns = array();

		foreach ( $responses as $response ) {
			foreach ( $response as $column => $value ) {
				if ( ! isset( $columns[ $column ] ) ) {
					$columns[ $column ] = count( $columns );
				}
			}
		}

		return array_flip( $columns );
	}

	/**
	 * @param array $responses
	 * @param array $columns
	 * @return array
	 */
	private function prepareDataForSpreadSheet( array $responses, array $columns ): array {
		$data = array( $columns );

		foreach ( $responses as $response ) {
			$row = array();
			foreach ( $columns as $column ) {
				$row[] = $response[ $column ] ?? '';
			}
			$data[] = $row;
		}

		return $data;
	}

	private function setHeaders( $filename ) {
		header( 'Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' );
		header( 'Content-Disposition: attachment;filename="' . $filename . '"' );
		header( 'Cache-Control: max-age=0' );
		header( 'Cache-Control: max-age=1' );
		header( 'Cache-Control: cache, must-revalidate' );
		header( 'Pragma: public' );
	}

}
