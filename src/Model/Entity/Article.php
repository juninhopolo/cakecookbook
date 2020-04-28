<?php
    namespace App\Model\Entity;

use Cake\Collection\Collection;
use Cake\ORM\Entity;

    class Article extends Entity{
        //Indica ao cake como as propriedades podem ser copiadas e persistidas da requisição para a entidade;
        protected $_accessible = [
            '*' => true,
            'id' => false,
            'slug' => false,
            'tag_string' => true
        ];

        protected function _getTagString(){
            if(isset($this->_properties['tag_string'])){
                return $this->_properties['tag_string'];
            }

            if(empty($this->tags)){
                return '';
            }

            $tags = new Collection($this->tags);
            $str = $tags->reduce(function($string, $tag){
                return $string . $tag->title . ', ';
            }, '');

            return trim($str, ', ');
        }
    }
?>