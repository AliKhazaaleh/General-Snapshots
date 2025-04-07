<?php

/*
 * Date: 2021
*/

class AutocompleteLocations
{
	static $autocompleteLocationsCacheKey='autocomplete_locations_tree_v1';
	private $trie;
	private $language;
	public static $keyOfChildern='*';
	public static $keyOfValues='+';
	
	public function __construct($lang='en')
	{
		$this->language=$lang;
		if(!empty($alllocationsData=Yii::app()->RedisCacheMain4->get(static::$autocompleteLocationsCacheKey.$this->language)))
			$this->trie=json_decode($alllocationsData, true);
		else
			$this->buildTrieLoctions();
	}
	
	public function buildTrieLoctions()
	{
		$locations=static::getLocations($this->language);
		foreach ($locations as $iso => $location)
		{
			$partsLocation=explode(' - ', $location);
			$location=static::normalizationText($partsLocation[count($partsLocation)-1]);
			$partsLocation=explode(' ', $location);
			
			foreach (static::getTextLoctionsInAllCasesOrder($partsLocation) as $key)
				$this->add($key, $iso);
		}
		/*
		 * * I removed the nodes of these characters because contains non-UTF-8 will lead to empty results at json_encode
		 * I will fix it when re-receive this ticket to execute that.
		 */
		unset($this->trie['*']['H']);
		unset($this->trie['*']['S']);
		
		Yii::app()->RedisCacheMain4->set(static::$autocompleteLocationsCacheKey.$this->language, json_encode($this->trie));
	}
	
	public function add($key, $value=null)
	{
		$key=strval($key);
		$keyLen = strlen($key);
		
		if (empty($key) || $keyLen<2)
			return;
		
		$trieNode=&$this->trie;
		$index=0;
		while ($index < $keyLen)
		{
			$character = $key[$index++];
			if (!isset($trieNode[static::$keyOfChildern][$character])) {
				$trieNode[static::$keyOfChildern][$character]=[static::$keyOfChildern=>[]];
			}
			$trieNode=&$trieNode[static::$keyOfChildern][$character];
		}
		
		if (!isset($trieNode[static::$keyOfValues]))
			$trieNode[static::$keyOfValues]=[$value];
		else
			$trieNode[static::$keyOfValues][]=$value;
	}
	
	public function search($text)
	{
		$text=static::normalizationText($text);
		$node=$this->trie;
		$textLen=strlen($text);
		$index=0;
		while ($index < $textLen)
		{
			$character=$text[$index++];
			if (!empty($node[static::$keyOfChildern][$character]))
			{
				$node=$node[static::$keyOfChildern][$character];
			}
			else
			{
				$node=[];
				break;
			}
		}
		return static::getValuesOfNode($node);
	}
	
	private static function getValuesOfNode($node)
	{
		$allValues=($node[static::$keyOfValues]??[]);
		foreach (($node[static::$keyOfChildern]??[]) as $child)
			$allValues=array_merge($allValues, static::getValuesOfNode($child));
		return $allValues;
	}
	private static function normalizationText($text)
	{
		/*
		 * Maybe will need to remove stop words
		 * $partsLocation=TextUtil::removeStopWords($partsLocation);
		 * 
		 * we need to implement arabic case
		 * remove "ال"
		 * replace
		"أ":"ا",
		"آ":"ا",
		"إ":"ا",
		"ة":"ه",
		"ى":"ي",
		"ئ":"ا",
		"ؤ":"و"
		*/
		return trim(strtoupper(TextUtil::cleanKeyword($text)));
	}
	
	private static function getTextLoctionsInAllCasesOrder($partsTemps)
	{
		$parts=[];
		foreach ($partsTemps as $part)
			if (!empty($part))
				$parts[]=$part;
		
		$allCases=[];
		$countPartsLocation=count($parts);
		if ($countPartsLocation==1)
		{
			$allCases=[
					$parts[0]
			];
		}
		elseif ($countPartsLocation==2)
		{
			$allCases=[
					$parts[0].' '.$parts[1],
					$parts[1].' '.$parts[0]
			];
		}
		elseif ($countPartsLocation>2)
		{
			$allCases=[
					$parts[0].' '.$parts[1].' '.$parts[2],
//					$parts[0].' '.$parts[2].' '.$parts[1],
//					$parts[1].' '.$parts[2].' '.$parts[0],
// 					$parts[1].' '.$parts[0].' '.$parts[2],
// 					$parts[2].' '.$parts[0].' '.$parts[1],
// 					$parts[2].' '.$parts[1].' '.$parts[0],
			];
		}
		return $allCases;
	}
	
	private static function getLocations($lang)
	{
		$countries=[];
		$all=LocationsCache::getAllLocations($lang);
		foreach ([
				'x5,0,0',
				'z0,0,0',
				'z1,0,0',
				'x0,0,0',
				'z2,0,0',
				'x4,0,0',
				'x1,0,0',
				'z12,0,0',
				'z13,0,0',
				'z14,0,0',
				'z15,0,0',
				'z16,0,0',
				'allIds',
				'all_ids',
				'allSlug'
		] as $iso)
			unset($all[$iso]);
		
		foreach ($all as $iso => $value)
		{
			$parts=explode(',', $iso);
			if (($parts[2]??0)==0)
				$countries[(($parts[1]??0)==0?$parts[0]:($parts[0].','.$parts[1]))]=$value;
		}
		return $countries;
	}
}
