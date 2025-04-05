<?php

use Foolz\SphinxQL\Facet;
use Foolz\SphinxQL\SphinxQL;
use Foolz\SphinxQL\MatchBuilder;


class SphinxActiveRecord extends \CComponent
{
	private $results;
    private $lastQueryCompiled;
	private static $connections=[];
	
	public function __construct($attrs=[], $attrsAliases=[])
	{
		$this->setAtrributes($attrs, $attrsAliases);
	}
	
	protected function setAtrributes($attrs, $attrsAliases=[])
	{
		foreach($attrs as $attrName => $attrValue)
		{
			if (is_string($attrAliase=array_search($attrName, $attrsAliases)))
				$attrName=$attrAliase;
			if (!property_exists($this,$attrName))
				error_log($attrName.' Not belong to Class '. get_class($this));
			elseif (!in_array($attrName,$this->localAttrs()))
				$this->$attrName=$attrValue;
		}
	}
	/**
	 * Returns the database connection used by sphinx active record.
	 * You must override this method to chose database connection becuase we aren't add default Sphinx.
	 * @return string the database connection used by sphinx active record.
	 */
	public function getDbConnection()
	{
		return null;
	}
	
	/**
	 * Returns the name of the associated database table.
	 * By default this method returns the class name as the table name.
	 * You may override this method if the table is not named after this convention.
	 * @return string the table name
	 */
	public function tableName()
	{
		$tableName = get_class($this);
		if(($pos=strrpos($tableName,'\\')) !== false)
			return substr($tableName,$pos+1);
		return $tableName;
	}

    public function getLastQueryCompiled()
    {
        return ($this->lastQueryCompiled??'');
    }
	/**
	 * @return static[]
	 */
	public function getResult()
	{
		return ($this->results['result']??[]);
	}
	
	/**
	 * @desc Total Rows Of The Search That Will Appear To The User With Details (Maximum Value Of Limit)
	 * @return integer
	 */
	public function getTotalResult()
	{
		return count($this->getResult());
	}
	
	/**
	 * @return array
	 */
	public function getFacets($col=null)
	{
		if($col!==null)
			return $this->results['facets'][$col]??[];
		return ($this->results['facets']??[]);
	}
	
	/**
	 * @desc Total Rows Of The Search Statement In Sphinx Layer
	 * @return integer
	 */
	public function getTotalFound()
	{
		return (int)($this->results['total_found']??0);
	}

	protected function localAttrs()
	{
		return ['results'];
	}
	/**
	 * @return SphinxActiveRecord
	 */
	public static function model()
	{
		return new static();
	}
	/**
	 * @param string $pool
	 * @return Foolz\SphinxQL\Drivers\Mysqli\Connection
	 */
	private function connect($pool)
	{
		$pool=$this->getDbConnection($pool);
		if(empty(self::$connections[$pool]))
		{
			$conn = new Foolz\SphinxQL\Drivers\Mysqli\Connection();
			$conn->setParams(Yii::app()->params['sphinxPools'][$pool]);
			$conn->connect(true);
			self::$connections[$pool]=$conn;
		}
		return self::$connections[$pool];
	}

	/**
	 * @param SphinxQL|array $sphinxCriteria
	 * @return SphinxActiveRecord
	 */
	public function find($sphinxCriteria, $withMeta=true)
	{
		if ($sphinxCriteria instanceof SphinxQL)
			$queryBuilder=$sphinxCriteria;
		else
		{
			$queryBuilder=new SphinxQL($this->connect($sphinxCriteria['pool']??null));
			$queryBuilder->select($selectWithAliases=SphinxActiveRecord::prepareSelect($sphinxCriteria['select']));
			$queryBuilder->from($this->tableName());

			if (($sphinxCriteria['match']??null) instanceof MatchBuilder)
				$queryBuilder->match($sphinxCriteria['match']);
				
			foreach ($sphinxCriteria['where']??[] as $whereItem)
				$queryBuilder->where($whereItem['col'], $whereItem['operator']??'=', $whereItem['value']);
			
			if (!empty($sphinxCriteria['groupBy']))
			{
				if (is_array($sphinxCriteria['groupBy']))
				{
					foreach ($sphinxCriteria['groupBy'] as $col)
						$queryBuilder->groupBy($col);
				}
				else
				{
					$queryBuilder->groupBy($sphinxCriteria['groupBy']);
				}
			}
				
			$queryBuilder->limit($sphinxCriteria['offset'], $sphinxCriteria['limit']);
				
				
			foreach ($sphinxCriteria['withinGroupOrderBy']??[] as $col => $direction)
				$queryBuilder->withinGroupOrderBy($col, $direction);
				
			foreach ($sphinxCriteria['orderBy']??[] as $col => $direction)
				$queryBuilder->orderBy($col, $direction);
				
			foreach ($sphinxCriteria['options']??[] as $name => $value)
				$queryBuilder->option($name, $value);
			
			$facetsKeys=[];
			foreach ($sphinxCriteria['facets']??[] as $facetItem)
			{
				$queryBuilder->facet((new Facet())
					->facet([[SphinxQL::expr($col=is_array($facetItem)?(is_array($facetItem['col'])?implode(',', $facetItem['col']):$facetItem['col']):$facetItem)]])
					->orderBy(SphinxQL::expr($facetItem['orderBy']??'count(*)'), $facetItem['direction']??'desc')
					->limit($facetItem['offset']??0, $facetItem['limit']??50));
				$facetsKeys[]=($facetItem['AS']??$col);
			}
			foreach ($sphinxCriteria['facetsFunc']??[] as $facetItem)
			{
				$params=array_merge([$col=$facetItem['col']], ($facetItem['params']??[]));
				$queryBuilder->facet((new Facet())
					->facetFunction($facetItem['func'], $params)
					->orderBy(SphinxQL::expr($facetItem['orderBy']??'count(*)'), $facetItem['direction']??'desc')
					->limit($facetItem['offset']??0, $facetItem['limit']??50));
				$facetsKeys[]=($facetItem['AS']??$col);
			}
		}
		/**
		 * TO PRINT THE SELECT STATEMENT
		 * error_log($queryBuilder->compile()->getCompiled());
		 */

        $this->lastQueryCompiled=$queryBuilder->compile()->getCompiled();

		try {
			$result=$queryBuilder->executeBatch();
		} catch (Exception $exception) {
			LoggingUtil::logBaytError('SphinxActiveRecord('.$this->getDbConnection($sphinxCriteria['pool']??null).')', 'find','SphinxActiveRecord.php', [
					'query'=>$queryBuilder->compile()->getCompiled(),
					'criteria'=>$sphinxCriteria,
			]);
			LoggingUtil::logException($exception);
			return null;
		}
		
		if (empty($indexCol=($sphinxCriteria['index']??null)))
		{
			foreach ($result->getNext() as $item)
				$this->results['result'][]=new static($item, $selectWithAliases);
		}
		else
		{
			foreach ($result->getNext() as $item)
			{
				$object=new static($item, $selectWithAliases);
				$this->results['result'][$object->$indexCol]=$object;
			}
		}
			
		$facetIndex=0;
		$this->results['facets']=[];
		while ($resultSetFacet=$result->getNext())
		{
			$this->results['facets'][$key=($sphinxCriteria['indexedFacets']??false)?$facetsKeys[$facetIndex]:$facetIndex]=[];
			foreach ($resultSetFacet as $item)
				$this->results['facets'][$key][]=$item;
			$facetIndex++;
		}
		
		if ($withMeta)
		{
			$queryBuilder->reset();
			$queryBuilder->query("SHOW META LIKE 'total_found'");
			$result=$queryBuilder->execute();
			$meta=$result->fetchNum();
			$this->results[$meta[0]]=($meta[1])??0;
		}
		
		return $this;
	}

	/**
	 * @param array $columns
	 * @return string[]
	 */
	private static function prepareSelect($columns = null)
	{
		$selectList=[];
		if (is_array($columns))
			foreach ($columns as $to => $from)
			{
				if (is_array($from))
				{
					if (is_int($to)) $to=$from['col'];
					$from=$from['func'].'('.implode(', ', $from['params']).')';
				}
				$selectList[$to]=strtolower($from);
			}
		return $selectList;
	}
}
