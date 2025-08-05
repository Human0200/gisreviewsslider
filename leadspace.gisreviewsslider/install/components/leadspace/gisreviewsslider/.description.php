<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();


$arComponentDescription = array(
	"NAME" => GetMessage("LEADSPACE_GISREVIEWSSLIDER_OTZYVY_MODULE_NAME"),
	"ID" => "leadspace",
	"DESCRIPTION" =>  GetMessage("LEADSPACE_GISREVIEWSSLIDER_OTZYVY_MODULE_NAME"),
	"ICON" => "",
	"SORT" => 1, 
 	"PATH" => array(
		"ID" => "leadspace",
		"CHILD" => array(
			"ID" => "leadspace",
			"NAME" => GetMessage("LEADSPACE_GISREVIEWSSLIDER_OTZYVY_SHORT_MODULE_NAME"),
		)
	),
);

?>