<?php

namespace VoidEngine;

$env   	  = new WFClass ('System.Environment', 'mscorlib');
$array 	  = $env->getCommandLineArgs ();
$size 	  = $array->length;
$selector = $array->selector;

for ($i = 0; $i < $size; ++$i)
    $params[] = VoidEngine::getArrayValue ($selector, $i);

VoidEngine::removeObjects ($selector);

$constants = [
	# Информация о программе
	
	'EXE_NAME'     => $APPLICATION->executablePath,
	'DOC_ROOT'     => dirname ($APPLICATION->executablePath),
	'IS_ADMIN'     => is_writable (getenv ('SystemRoot')),
	'START_PARAMS' => $params,
	'USERNAME'	   => $env->username,

	# Константы MessageBox'а
	
	'MB_YESNO' 			  => 4,
	'MB_YESNOCANCEL' 	  => 3,
	'MB_OK' 			  => 0,
	'MB_OKCANCEL' 		  => 1,
	'MB_RETRYCANCEL' 	  => 5,
	'MB_ABORTRETRYIGNORE' => 2,
	'MBI_ASTERISK' 		  => 64,
	'MBI_ERROR' 		  => 16,
	'MBI_EXCLAMATION' 	  => 48,
	'MBI_HAND' 			  => 16,
	'MBI_INFORMATION' 	  => 64,
	'MBI_NONE' 			  => 0,
	'MBI_QUESTION' 		  => 32,
	'MBI_STOP' 			  => 16,
	'MBI_WARNING' 		  => 48,
	'MBDB_1' 			  => 0,
	'MBDB_2' 			  => 256,
	'MBDB_3' 			  => 512,
	
	# Константы DialogResult
	
	'drOk'	   => 1,
	'drYes'    => 6,
	'drNo' 	   => 7,
	'drCancel' => 2,
	'drRetry'  => 4,
	'drAbort'  => 3,
	'drIgnore' => 5,
	'drNone'   => 0,
	
	# Константы форм
	
	'fbsNone'			   => 0,
	'fbsSingle'			   => 1,
	'fbs3D'				   => 2,
	'fbsDialog'			   => 3,
	'fbsSizable'		   => 4,
	'fbsToolWindow'		   => 5,
	'fbsSizableToolWindow' => 6,

	'fwsNormal'	   => 0,
	'fwsMinimized' => 1,
	'fwsMaximized' => 2,

	'fspManual' 				=> 0,
	'fspCenterScreen' 			=> 1,
	'fspWindowsDefaultLocation' => 2,
	'fspWindowsDefaultBounds'   => 3,
	'fspCenterParent' 			=> 4,

	# Константы стиля оконтовки

	'bsNone' 		=> 0,
	'bsFixedSingle' => 1,
	'bsFixed3D' 	=> 2,

	# Константы выравнивания

	'alTopLeft' 	 => 1,
	'alTopCenter' 	 => 2,
	'alTopRight' 	 => 4,
	'alMiddleLeft' 	 => 16,
	'alMiddleCenter' => 32,
	'alMiddleRight'  => 64,
	'alBottomLeft' 	 => 256,
	'alBottomCenter' => 512,
	'alBottomRight'  => 1024,

	# Константы притягивания (Anchor)

	'acNone'   => 0,
	'acTop'	   => 1,
	'acBottom' => 2,
	'acLeft'   => 4,
	'acRight'  => 8,

	# Константы SizeMode-параметра PictureBox'а

	'smNormal' 		 => 0,
	'smStretchImage' => 1,
	'smAutoSize' 	 => 2,
	'smCenterImage'  => 3,
	'smZoom' 		 => 4,

	# Константы View-параметра ListView'а
	
	'vwLargeIcon' => 0,
	'vwDetails'	  => 1,
	'vwSmallIcon' => 2,
	'vwList' 	  => 3,
	'vwTile' 	  => 4,

	# Константы dropDownStyle компонента ComboBox

	'ddSimple'		 => 0,
	'ddDropDown' 	 => 1,
	'ddDropDownList' => 2,

	# Константы причины закрытия формы
	
	'fcrNone'                => 0,
	'fcrWindowsShutDown'     => 1,
	'fcrMdiFormClosing'      => 2,
	'fcrUserClosing'         => 3,
	'fcrTaskManagerClosing'  => 4,
	'fcrFormOwnerClosing'    => 5,
	'fcrApplicationExitCall' => 6,
	
	# Константы дока
	
	'dsBottom' => 2,
	'dsFill'   => 5,
	'dsLeft'   => 3,
	'dsNone'   => 0,
	'dsRight'  => 4,
	'dsTop'    => 1,
	
	# Константы FCTB.Language
	
	'langCSharp' => 1,
	'langCustom' => 2,
	'langHTML' 	 => 3,
	'langJS' 	 => 4,
	'langLua'	 => 5,
	'langPHP' 	 => 6,
	'langSQL' 	 => 7,
	'langVB' 	 => 8,
	'langXML' 	 => 9,
	
	# Константы видимости окна процесса
	
	'pwsHidden'    => 0,
	'pwsMaximized' => 3,
	'pwsMinimized' => 2,
	'pwsNormal'    => 1,
	
	# Константы запуска
	
	'runNo'     => 0,
	'runYes'    => 1,
	'runThread' => 2,

	# Константы стиля ProgressBar'а

	'pbBlocks'	   => 0,
	'pbContinuous' => 1,
	'pbMarquee'    => 2,

	# Константы свойства FlatStyle
	
	'flFlat'	 => 0,
	'flPopup'	 => 1,
	'flStandard' => 2,
	'flSystem'   => 3,

	# Константы свойства DrawMode

	'dwNormal'			  => 0,
	'dwOwnerDrawFixed'	  => 1,
	'dwOwnerDrawVariable' => 2,

	# Цветовые константы
	
	'clAliceBlue'			 => 0xFFF0F8FF,
	'clAntiqueWhite'		 => 0xFFFAEBD7,
	'clAqua'				 => 0xFF00FFFF,
	'clAquamarine'			 => 0xFF7FFFD4,
	'clAzure'				 => 0xFFF0FFFF,
	'clBeige'				 => 0xFFF5F5DC,
	'clBisque'				 => 0xFFFFE4C4,
	'clBlack'				 => 0xFF000000,
	'clBlanchedAlmond'		 => 0xFFFFEBCD,
	'clBlue'				 => 0xFF0000FF,
	'clBlueViolet'			 => 0xFF8A2BE2,
	'clBrown'				 => 0xFFA52A2A,
	'clBurlyWood'			 => 0xFFDEB887,
	'clCadetBlue' 			 => 0xFF5F9EA0,
	'clChartreuse'			 => 0xFF7FFF00,
	'clChocolate'			 => 0xFFD2691E,
	'clCoral'				 => 0xFFFF7F50,
	'clCornflowerBlue'		 => 0xFF6495ED,
	'clCornsilk'			 => 0xFFFFF8DC,
	'clCrimson'				 => 0xFFDC143C,
	'clCyan'				 => 0xFF00FFFF,
	'clDarkBlue'			 => 0xFF00008B,
	'clDarkCyan'			 => 0xFF008B8B,
	'clDarkGoldenrod'		 => 0xFFB8860B,
	'clDarkGray'			 => 0xFFA9A9A9,
	'clDarkGreen'			 => 0xFF006400,
	'clDarkKhaki'			 => 0xFFBDB76B,
	'clDarkMagenta'			 => 0xFF8B008B,
	'clDarkOliveGreen'		 => 0xFF556B2F,
	'clDarkOrange'			 => 0xFFFF8C00,
	'clDarkOrchid'			 => 0xFF9932CC,
	'clDarkRed'				 => 0xFF8B0000,
	'clDarkSalmon'			 => 0xFFE9967A,
	'clDarkSeaGreen'		 => 0xFF8FBC8F,
	'clDarkSlateBlue'		 => 0xFF483D8B,
	'clDarkSlateGray'		 => 0xFF2F4F4F,
	'clDarkTurquoise'		 => 0xFF00CED1,
	'clDarkViolet'			 => 0xFF9400D3,
	'clDeepPink'			 => 0xFFFF1493,
	'clDeepSkyBlue'			 => 0xFF00BFFF,
	'clDimGray'				 => 0xFF696969,
	'clDodgerBlue'			 => 0xFF1E90FF,
	'clFirebrick'			 => 0xFFB22222,
	'clFloralWhite'			 => 0xFFFFFAF0,
	'clForestGreen'			 => 0xFF228B22,
	'clFuchsia'				 => 0xFFFF00FF,
	'clGainsboro'			 => 0xFFDCDCDC,
	'clGhostWhite'			 => 0xFFF8F8FF,
	'clGold'				 => 0xFFFFD700,
	'clGoldenrod'			 => 0xFFDAA520,
	'clGray'				 => 0xFF808080,
	'clGreen'				 => 0xFF008000,
	'clGreenYellow'			 => 0xFFADFF2F,
	'clHoneydew'			 => 0xFFF0FFF0,
	'clHotPink'				 => 0xFFFF69B4,
	'clIndianRed'			 => 0xFFCD5C5C,
	'clIndigo'				 => 0xFF4B0082,
	'clIvory'				 => 0xFFFFFFF0,
	'clKhaki'				 => 0xFFF0E68C,
	'clLavender'			 => 0xFFE6E6FA,
	'clLavenderBlush'		 => 0xFFFFF0F5,
	'clLawnGreen'			 => 0xFF7CFC00,
	'clLemonChiffon'		 => 0xFFFFFACD,
	'clLightBlue'			 => 0xFFADD8E6,
	'clLightCoral'			 => 0xFFF08080,
	'clLightCyan'			 => 0xFFE0FFFF,
	'clLightGoldenrodYellow' => 0xFFFAFAD2,
	'clLightGray' 			 => 0xFFD3D3D3,
	'clLightGreen'			 => 0xFF90EE90,
	'clLightPink' 			 => 0xFFFFB6C1,
	'clLightSalmon'			 => 0xFFFFA07A,
	'clLightSeaGreen'		 => 0xFF20B2AA,
	'clLightSkyBlue'		 => 0xFF87CEFA,
	'clLightSlateGray'		 => 0xFF778899,
	'clLightSteelBlue'		 => 0xFFB0C4DE,
	'clLightYellow'			 => 0xFFFFFFE0,
	'clLime'				 => 0xFF00FF00,
	'clLimeGreen'			 => 0xFF32CD32,
	'clLinen'				 => 0xFFFAF0E6,
	'clMagenta'				 => 0xFFFF00FF,
	'clMaroon'				 => 0xFF800000,
	'clMediumAquamarine'	 => 0xFF66CDAA,
	'clMediumBlue' 			 => 0xFF0000CD,
	'clMediumOrchid'		 => 0xFFBA55D3,
	'clMediumPurple'		 => 0xFF9370DB,
	'clMediumSeaGreen'		 => 0xFF3CB371,
	'clMediumSlateBlue'		 => 0xFF7B68EE,
	'clMediumSpringGreen'	 => 0xFF00FA9A,
	'clMediumTurquoise'		 => 0xFF48D1CC,
	'clMediumVioletRed'		 => 0xFFC71585,
	'clMidnightBlue'		 => 0xFF191970,
	'clMintCream'			 => 0xFFF5FFFA,
	'clMistyRose'			 => 0xFFFFE4E1,
	'clMoccasin'			 => 0xFFFFE4B5,
	'clNavajoWhite'			 => 0xFFFFDEAD,
	'clNavy'				 => 0xFF000080,
	'clOldLace'				 => 0xFFFDF5E6,
	'clOlive'				 => 0xFF808000,
	'clOliveDrab'			 => 0xFF6B8E23,
	'clOrange'				 => 0xFFFFA500,
	'clOrangeRed'			 => 0xFFFF4500,
	'clOrchid'				 => 0xFFDA70D6,
	'clPaleGoldenrod'		 => 0xFFEEE8AA,
	'clPaleGreen'			 => 0xFF98FB98,
	'clPaleTurquoise'		 => 0xFFAFEEEE,
	'clPaleVioletRed'		 => 0xFFDB7093,
	'clPapayaWhip'			 => 0xFFFFEFD5,
	'clPeachPuff'			 => 0xFFFFDAB9,
	'clPeru'				 => 0xFFCD853F,
	'clPink'				 => 0xFFFFC0CB,
	'clPlum'				 => 0xFFDDA0DD,
	'clPowderBlue'			 => 0xFFB0E0E6,
	'clPurple'				 => 0xFF800080,
	'clRed'					 => 0xFFFF0000,
	'clRosyBrown'			 => 0xFFBC8F8F,
	'clRoyalBlue'			 => 0xFF4169E1,
	'clSaddleBrown'			 => 0xFF8B4513,
	'clSalmon'				 => 0xFFFA8072,
	'clSandyBrown'			 => 0xFFF4A460,
	'clSeaGreen'			 => 0xFF2E8B57,
	'clSeaShell'			 => 0xFFFFF5EE,
	'clSienna'				 => 0xFFA0522D,
	'clSilver'				 => 0xFFC0C0C0,
	'clSkyBlue'				 => 0xFF87CEEB,
	'clSlateBlue'			 => 0xFF6A5ACD,
	'clSlateGray'			 => 0xFF708090,
	'clSnow'				 => 0xFFFFFAFA,
	'clSpringGreen'			 => 0xFF00FF7F,
	'clSteelBlue'			 => 0xFF4682B4,
	'clTan'					 => 0xFFD2B48C,
	'clTeal'				 => 0xFF008080,
	'clThistle'				 => 0xFFD8BFD8,
	'clTomato'				 => 0xFFFF6347,
	'clTurquoise'			 => 0xFF40E0D0,
	'clViolet'				 => 0xFFEE82EE,
	'clWheat'				 => 0xFFF5DEB3,
	'clWhite'				 => 0xFFFFFFFF,
	'clWhiteSmoke'			 => 0xFFF5F5F5,
	'clYellow'				 => 0xFFFFFF00,
	'clYellowGreen'			 => 0xFF9ACD32
];

foreach ($constants as $constantName => $constantValue)
	define ($constantName, $constantValue);

unset ($constants, $env, $array, $selector, $size, $params);

?>
