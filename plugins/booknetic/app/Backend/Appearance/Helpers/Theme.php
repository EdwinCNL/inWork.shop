<?php

namespace BookneticApp\Backend\Appearance\Helpers;

use BookneticApp\Backend\Appearance\Model\Appearance;
use BookneticApp\Providers\Backend;
use BookneticApp\Providers\Helper;

class Theme
{

	public static function getThemeCss( $appearanceId )
	{
		if( !self::checkThemeCssFileExist( $appearanceId ) )
		{
			self::createThemeCssFile( $appearanceId );
		}

		return Helper::uploadedFileURL( 'theme_' . $appearanceId . '.css' , 'Appearance' );
	}

	private static function checkThemeCssFileExist( $appearanceId )
	{
		$filePath = Helper::uploadedDir( 'Appearance' ) . '/theme_' . (int)$appearanceId . '.css';

		return file_exists( $filePath );
	}

	private static function cssFilePath( $appearanceId )
	{
		return Helper::uploadedDir( 'Appearance' ) . '/theme_' . (int)$appearanceId . '.css';
	}

	public static function createThemeCssFile( $appearanceId )
	{
		$filePath = self::cssFilePath( $appearanceId );
		$themeInf = Appearance::get( $appearanceId );

		$themeColors = isset( $themeInf['colors'] ) ? json_decode( $themeInf['colors'], true ) : [];

		$default_colors = [
			'panel'					=>	'#292d32',
			'primary'				=>	'#6c70dc',
			'primary_txt'			=>	'#ffffff',
			'active_steps'			=>	'#4fbf65',
			'active_steps_txt'		=>	'#4fbf65',
			'compleated_steps'		=>	'#6c70dc',
			'compleated_steps_txt'	=>	'#ffffff',
			'other_steps'			=>	'#4d545a',
			'other_steps_txt'		=>	'#626c76',
			'title'					=>	'#292d32',
			'border'				=>	'#53d56c',
			'price'					=>	'#53d56c'
		];

		foreach ( $default_colors AS $color_name => $color )
		{
			if( isset( $themeColors[ $color_name ] ) && is_string( $themeColors[ $color_name ] ) && preg_match('/\#[a-zA-Z0-9]{1,8}/', $themeColors[ $color_name ]) )
			{
				$default_colors[ $color_name ] = $themeColors[ $color_name ];
			}
		}

		$themeColors = $default_colors;

		foreach ( $themeColors AS $tKey => $tVal )
		{
			$themeColors[ '%%' . $tKey . '%%' ] = htmlspecialchars( $tVal );

			unset( $themeColors[ $tKey ] );
		}

		$themeColors['%%id%%'] = $appearanceId;
		$themeColors['%%height%%'] = (int)$themeInf['height'];
		$themeColors['%%fontfamily%%'] = isset($themeInf['fontfamily']) ? $themeInf['fontfamily'] : '';
		$themeColors['%%custom_css%%'] = isset($themeInf['custom_css']) ? $themeInf['custom_css'] : '';

		$cssFileContent = self::prepareCssFile( $themeColors );

		file_put_contents( $filePath , $cssFileContent );
	}

	private static function prepareCssFile( $colors )
	{
		$tplCssFile = file_get_contents( Backend::MODULES_DIR . '/Appearance/assets/css/theme.css.tpl' );

		return str_replace( array_keys( $colors ), array_values( $colors ) , $tplCssFile );
	}

}