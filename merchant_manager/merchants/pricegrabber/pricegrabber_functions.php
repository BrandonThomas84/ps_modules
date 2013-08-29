<?php

//start pricegrabber custom functions
function priceGrabberCategorization($alias) {
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
function priceGrabberAvailability($alias) {
	return "(case
            when (`a1`.`quantity` > 0) then 'Yes'
            when
                ((`a1`.`quantity` = 0)
                    or isnull(`a1`.`quantity`))
            then
                'No'
            else 'No'
        end) AS `" . $alias . "`";
}
function priceGrabberShippingCost($alias) {
	return "cast((case
                when (`a1`.`price` >= 99) then '0.00'
                else '4.99'
            end)
            as decimal (12 , 2 )) AS `" . $alias . "`";
}
	

?>