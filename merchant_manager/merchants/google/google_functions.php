<?php

//begin google custom functions
function googleAvailability($alias){
	return "(case
		when (`a1`.`quantity` > 0) then 'in stock'
		when
			((`a1`.`quantity` = 0)
				or isnull(`a1`.`quantity`))
		then
			'out of stock'
		else 'out of stock'
	end) AS `" . $alias . "`";
	}
function googleIdentifier_exists($alias){
	return "(case
		when
			((`a1`.`reference` is not null)
				or (`a1`.`reference` <> ''))
		then
			'TRUE'
		when
			((`a1`.`upc` is not null)
				or (`a1`.`upc` <> ''))
		then
			'TRUE'
		else 'FALSE'
	end) AS `" . $alias . "`";
	}
	
?>