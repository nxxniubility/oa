<?php

namespace Common\Model;
use Common\Model\SystemModel;
class MoveModel extends SystemModel{

	public function array_depth($array) 
	{
		$max_depth = 1;
		foreach ($array as $value)
		{
			if (is_array($value))
			{
				$depth = array_depth($value)+1;
				if ($depth > $max_depth) {
					$max_depth = $depth;
				}
			}
		}        
		return $max_depth;
	}


	//

}