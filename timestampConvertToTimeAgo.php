<?php
	 /*
	 * Convert a given timestamp of format 2000-10-20 23:59:59 to "x hours/minutes/days/months/years" ago
	 * Accuracy defines how many periods you should return. Ex: Accuracy 3 = x months y days z hours ago
	 * Now is if you want to specify the time to compare with, if null then uses current time
	 */
	 
	DEFINE(LANG, 'en');
	 
	function timestampConvertToTimeAgo($timestamp, $accuracy = 6, $now = Null)
	{
		if( LANG == 'en')
		{
			$periods = array("s" => "second", "i" => "minute", "h" => "hour", "d" => "day", "m" => "month", "y" => "year");
		}
		else
		{
			$periods = array("s" => "seconde", "i" => "minute", "h" => "heure", "d" => "jour", "m" => "mois", "y" => "année");
		}
			
		if ($now === Null) 
		{
			$now = date('Y-m-d H:i:s');
		}
		
		// get an array with the number of years/months/days/hours/minutes/seconds difference between dates
		$d1 = new DateTime( $timestamp );
		$d2 = new DateTime( $now );
		$diff = $d2->diff($d1);
		
		$result = '';
		$i = 0;
		foreach($diff as $k => $v)
		{
			// check if the key is in our periods array ($diff also contains other information like if its a weekday)
			// also check if the value is not 0 to skip through differences where it has not been a year/month/etc yet
			if( array_key_exists($k, $periods) && $v )
			{
				// less than an hour ago		
				if( ( ($k == 'i' || $k == 's') && $result == '')  && $accuracy == 1 )
				{
					if(LANG == 'en')
						$result = 'moments';
					else
						$result = 'quelques instants';
				}
				
				// if accuracy is 1 and its been an hour
				elseif($v == 1 && $periods[$k] == 'hour' && $accuracy == 1)
				{
					$result .= 'an ' . $periods[$k];
				}
				elseif($v == 1 && $periods[$k] == 'heure' && $accuracy == 1)
				{
					$result .= 'une ' . $periods[$k];
				}
				
				// french 1 year is 'ans' instead of 'annee'
				elseif($v == 1 && $periods[$k] == 'année')
				{
					$result .= 'un ans';
				} 
				
				// make plural if larger than 1
				elseif($v > 1 && $periods[$k] != 'mois') // dont make 'mois' plural
				{
					$result .= $v . ' ' . $periods[$k] . 's';
				}
				// otherwise its 1 unit so not plural
				else
				{
					$result .= $v . ' ' . $periods[$k];
				}
				
				// add a space between periods 
				if($accuracy > 1)
					$result .= ' ';
				
				$i++;
					
			}
			
			//break the loop if desired accuracy is reached
			if($i == $accuracy)
			{
				break;
			}
		}
		
		// datetime stamps are exactly the same so $result is empty.
		if($d1 == $d2)
		{
			if(LANG == 'en')
				$result = 'just now';
			else 
				$result = 'juste maintenant';
		}
		// if we have a string in result append the ago/future
		elseif($result)
		{
			// if invert is 0, then timestamp was in the future (timestamp > now)
			if( $diff->invert == 0 )
			{
				if(LANG == 'en')
					$result = $result . ' in the future';
				else 
					$result = $result . ' dans le future';
			}
			else
			{
				if(LANG == 'en')
					$result = $result . ' ago';
				else 
					$result = 'il y a ' . $result;
			}
		}
		else
			$result = 'undefined'; // fallback if everything fails
			
		
		return $result;
	}

	//echo timestampConvertToTimeAgo( '2012-08-06 19:44:00' );
?>
