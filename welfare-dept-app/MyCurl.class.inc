<?php

class MyCurl {

	/**
	 * curl multi 実行
	 * @param  $url_list
	 * @retval array
	 */
	public static function execMulti( $url_list ) {
		// マルチハンドルの用意
		$mh = curl_multi_init();
		// URLをキーとして、複数のcURLハンドルを登録
		$ch_list = array();
		foreach( $url_list as $id => $url ) {
			self::setoptSimpleReq( $id, $url, $mh, $ch_list );
		}
		// 一括で通信実行、全て終わるのを待つ
		$running = null;
		do {
			curl_multi_exec( $mh, $running );
		} while( $running );
		// 一括で通信実行後にエラー情報の拾い上げ
		$info_list = array();
		do {
			$info = curl_multi_info_read( $mh );
			if ( $info ) {
				$info_list[ $info[ 'handle' ] ] = $info;
			} else {
				break;
			}
		} while( true );
		// 実行結果の取得
		foreach( $ch_list as $id => $ch ) {
			$results[ $id ][ 'url' ] = $url_list[ $id ];
			$results[ $id ][ 'curl_errno' ] = $info_list[ $ch ][ 'result' ];
			$info = curl_getinfo( $ch );
			$results[ $id ][ 'http_code' ] = $info[ 'http_code' ];
			$tran = curl_multi_getcontent( $ch );
			$resp = array();
			$resp[] = substr( $tran, 0, $info[ 'header_size' ] );
			$resp[] = substr( $tran, $info[ 'header_size' ] );
			$results[ $id ][ 'head' ] = 
				( count( $resp ) > 0 ? $resp[ 0 ] : null );
			$results[ $id ][ 'body' ] =
				( count( $resp ) > 1 ? $resp[ 1 ] : null );
			curl_multi_remove_handle( $mh, $ch );
			curl_close( $ch );
		}
		// マルチハンドルの後始末
		curl_multi_close( $mh );
		return $results;
	}

	/**
	 * シンプルなGETリクエストのオプション設定
	 */
	private static function setoptSimpleReq( $id, $url, &$mh, &$ch_list ) {
		$ch_list[ $id ] = curl_init( $url );
		curl_setopt($ch_list[ $id ], CURLOPT_HEADER, true);
		curl_setopt($ch_list[ $id ], CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch_list[ $id ], CURLOPT_TIMEOUT, true);
		curl_multi_add_handle( $mh, $ch_list[ $id ] );
//		curl_setopt($ch_list[ $id ], CURLOPT_POST, true);
//		curl_setopt($ch_list[ $id ], CURLOPT_POSTFIELDS, $data);
		return;
	}

}
