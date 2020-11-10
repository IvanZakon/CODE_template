<?
//Отключаем статистику Bitrix
define("NO_KEEP_STATISTIC", true);
//Подключаем движок
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
//устанавливаем тип ответа как xml документ
header('Content-Type: application/xml; charset=utf-8');


$array_pages = array();

//Простые текстовые страницы: начало
$array_pages[] = array(
   	'NAME' => 'Главная страница',
   	'URL' => '/',
);
$array_pages[] = array(
	'NAME' => 'О КОМПАНИИ',
	'URL' => '/about/',
);
$array_pages[] = array(
	'NAME' => 'УСЛОВИЕ ОПЛАТЫ',
	'URL' => '/buyer/',
);
$array_pages[] = array(
	'NAME' => 'КОНТАКТЫ',
	'URL' => '/about/contacts/',
);
$array_pages[] = array(
	'NAME' => 'ОТЗЫВЫ',
	'URL' => '/about/reviews/',
);
$array_pages[] = array(
	'NAME' => 'БЛОГ',
	'URL' => '/about/blog/',
);
$array_pages[] = array(
	'NAME' => 'ВИДЕО',
	'URL' => '/about/video/',
);
$array_pages[] = array(
	'NAME' => 'LOOKBOOK',
	'URL' => '/about/lookbook/',
);
$array_pages[] = array(
	'NAME' => 'ДОСТАВКА',
	'URL' => '/buyer/delivery/',
);
$array_pages[] = array(
	'NAME' => 'ВОЗВРАТ',
	'URL' => '/buyer/refund/',
);
$array_pages[] = array(
	'NAME' => 'ОФЕРТА',
	'URL' => '/buyer/oferta/',
);
$array_pages[] = array(
	'NAME' => 'ПОЛИТИКА КОНФИДЕНЦИАЛЬНОСТИ',
	'URL' => '/buyer/politika-konfidentsialnosti/',
);
$array_pages[] = array(
	'NAME' => 'СОГЛАСИЕ НА ОБРАБОТКУ ДАННЫХ',
	'URL' => '/buyer/soglasie/',
);
//Простые текстовые страницы: конец


$array_iblocks_id = array('20', '21', '7', '26', '3', '6', '5', '4', '9', '27', '25', '23', '24', '22', '10', '8', '2', '1'); //ID инфоблоков, разделы и элементы которых попадут в карту сайта
if(CModule::IncludeModule("iblock"))
{
	foreach($array_iblocks_id as $iblock_id)
	{
		//Список разделов
   		$res = CIBlockSection::GetList(
	      	array(),
	      	Array(
	         	"IBLOCK_ID" => $iblock_id,
	         	"ACTIVE" => "Y"
	      	),
      		false,
    		array(
    		"ID",
    		"NAME",
    		"SECTION_PAGE_URL",
    		"TIMESTAMP_X"
    	));
   		while($ob = $res->GetNext())
   		{
			$array_pages_iblock[] = array(
			   	'NAME' => $ob['NAME'],
			   	'URL' => $ob['SECTION_PAGE_URL'],
			   	'LASTMOD' => $ob['TIMESTAMP_X']
			);
   		}
		//Список элементов
   		$res = CIBlockElement::GetList(
	      	array(),
	      	Array(
	         	"IBLOCK_ID" => $iblock_id,
	         	"ACTIVE_DATE" => "Y",
	         	"ACTIVE" => "Y"
	      	),
      		false,
      		false,
    		array(
    		"ID",
    		"NAME",
    		"DETAIL_PAGE_URL",
    		"TIMESTAMP_X"
    	));
   		while($ob = $res->GetNext())
   		{
			$array_pages_iblock[] = array(
			   	'NAME' => $ob['NAME'],
			   	'URL' => $ob['DETAIL_PAGE_URL'],
			   	'LASTMOD' => $ob['TIMESTAMP_X']
			);
   		}
	}
}

//Создаём XML документ: начало
//echo '<pre>'; print_r($array_pages); echo '</pre>';
$xml_content = '';
$xml_content_iblock = '';
$changefreq = 'daily';
$dateformat = 'Y-m-d';
$site_url = 'https://'.$_SERVER['HTTP_HOST'];
$quantity_elements = 0;
foreach($array_pages as $v){
	$quantity_elements++;
	if ($quantity_elements == 1){
		$priority = 1;
	} else {
		$priority = 0.8;
	}
	$page_url = mb_substr( $v['URL']."index.php", 1);
	$lastmod = date($dateformat, filemtime($page_url));
	$xml_content.='
		<url>
			<loc>'.$site_url.$v['URL'].'</loc>
			<changefreq>'.$changefreq.'</changefreq>
			<lastmod>'.$lastmod.'</lastmod>
			<priority>'.$priority.'</priority>
		</url>
	';
}
foreach($array_pages_iblock as $v){
	$quantity_elements++;
	$priority = 0.8;
	$lastmod = date($dateformat, MakeTimeStamp($v['LASTMOD'], "DD.MM.YYYY HH:MI:SS"));
	$xml_content_iblock.='
		<url>
			<loc>'.$site_url.$v['URL'].'</loc>
			<changefreq>'.$changefreq.'</changefreq>
			<lastmod>'.$lastmod.'</lastmod>
			<priority>'.$priority.'</priority>
			
		</url>
	';
}
$quantity_elements = 0;

//Создаём XML документ: конец

//Выводим документ
echo '<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="https://www.sitemaps.org/schemas/sitemap/0.9">
	'.$xml_content.''.$xml_content_iblock.'
</urlset>
';
?>