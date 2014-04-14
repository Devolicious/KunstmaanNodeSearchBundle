<?php
/**
 * Created by PhpStorm.
 * User: ruud
 * Date: 04/03/14
 * Time: 18:18
 */

namespace Kunstmaan\NodeSearchBundle\Search;


use Elastica\Query;
use Elastica\Search;
use Elastica\Suggest;

abstract class AbstractElasticaSearcher
{
    protected   $indexName;
    protected   $indexType;
    protected   $type;
    protected   $provider;

    protected   $query;
    protected   $data;
    protected   $language;
    protected   $contentType;

    public function __construct()
    {
        $this->query = new Query();
    }

    public function search($offset = null, $size = null){
        $this->defineSearch($this->data, $this->language, $this->contentType);
        $this->setPagination($offset, $size);

        $index = $this->provider->getIndex($this->getIndexName());
        $search = new Search($this->provider->getClient());
        $search->addIndex($index);
        $search->addType($index->getType($this->indexType.'_'.$this->language));

        $result = $search->search($this->query);

        return $result;
    }

    public function getSuggestions()
    {
        $suggestPrase = new Suggest\Phrase('content-suggester', 'content');
        $suggestPrase->setText($this->data);
        $suggestPrase->setAnalyzer('suggestion_analyzer_'.$this->language);
        $suggestPrase->setHighlight("<strong>", "</strong>");
        $suggestPrase->setConfidence(2);
        $suggestPrase->setSize(1);

        $suggest = new Suggest($suggestPrase);
        $this->query->setSuggest($suggest);

        $index = $this->provider->getIndex($this->getIndexName());
        $search = new Search($this->provider->getClient());
        $search->addIndex($index);
        $search->addType($index->getType($this->indexType.'_'.$this->language));

        $result = $search->search($this->query);

        return $result;
    }

    public function setPagination($offset, $size)
    {
        if (is_int($offset)) {
            $this->query->setFrom($offset);
        }

        if (is_int($size)) {
            $this->query->setSize($size);
        }
    }

    /**
     * @param mixed $indexName
     */
    public function setIndexName($indexName)
    {
        $this->indexName = $indexName;
    }

    /**
     * @return mixed
     */
    public function getIndexName()
    {
        return $this->indexName;
    }

    /**
     * @param mixed $indexType
     */
    public function setIndexType($indexType)
    {
        $this->indexType = $indexType;
    }

    /**
     * @return mixed
     */
    public function getIndexType()
    {
        return $this->indexType;
    }

    /**
     * @param mixed $provider
     */
    public function setProvider($provider)
    {
        $this->provider = $provider;
    }

    /**
     * @return mixed
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $language
     */
    public function setLanguage($language)
    {
        $this->language = $language;
    }

    /**
     * @return mixed
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $contentType
     */
    public function setContentType($contentType)
    {
        $this->contentType = $contentType;
    }

    /**
     * @return mixed
     */
    public function getContentType()
    {
        return $this->contentType;
    }

}