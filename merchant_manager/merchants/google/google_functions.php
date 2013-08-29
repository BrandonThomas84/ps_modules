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
function googleProduct_type($alias){
	return "(case
		when isnull(`a5`.`catName1`) then NULL
		when isnull(`a5`.`catName2`) then `a5`.`catName1`
		when isnull(`a5`.`catName3`) then concat(`a5`.`catName1`, ' > ', `a5`.`catName2`)
		when
			((`a5`.`catName1` is not null)
				and (`a5`.`catName2` is not null)
				and (`a5`.`catName3` is not null))
		then
			concat(`a5`.`catName1`,
					' > ',
					`a5`.`catName2`,
					' > ',
					`a5`.`catName3`)
		else NULL
	end) AS `" . $alias . "`";
	}
	

?>