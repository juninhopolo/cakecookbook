<?php
    namespace App\Model\Table;

    use Cake\ORM\Table;
    use Cake\ORM\Query;

    //Classe Util do Cake para operações com texto;
    use Cake\Utility\Text;

class ArticlesTable extends Table{
        public function initialize(array $config){
            //Como existe os campos "created" e "modified" nas tabelas o Cake faz o bind;
            $this->addBehavior('Timestamp');
            //"dependent" diz para deletar as relações de articles_tags caso o artigo seja deletado;
            $this->belongsToMany('Tags', ['joinTable' => 'articles_tags', 'dependent' => true]);
        }

        //Função executada antes de qualquer salvamento em banco, tanto inserts com updates.
        //Pode ser usada para tratar algum dado e/ou fazer calculos que vão ser gravados em um campo;
        public function beforeSave($event, $entity, $option){
            if($entity->isNew() && !$entity->slug){
                $sluggedTitle = Text::slug($entity->title);
                $entity->slug = substr($sluggedTitle, 0, 191);
            }

            //Faz o parser do tag string para tags;
            if($entity->tag_string){
                $entity->tags = $this->_buildTags($entity->tag_string);

            }
        }

        //Essa função não é chamada pelo controllers, por isso protected;
        protected function _buildTags($tagString){
            //Temos $tagString = 'xxx , yyy, ,zzz , zzz' por exemplo.

            $newTags = array_map('trim', explode(',', $tagString));
            //Temos agora $newTags = ['xxx','yyy', ,'zzz', 'zzz'].

            $newTags = array_filter($newTags);
            //Temos agora $newTags = ['xxx', 'yyy', 'zzz', 'zzz'].

            $newTags = array_unique($newTags);
            //Temos agora ['xxx', 'yyy', 'zzz'];
            
            $query = $this->Tags->find()->where(['Tags.title IN' => $newTags]);

            //Objetos de Query/Consulta só são executados quando chamados por um execute()
            //ou quando iterados por um for each;
            
            //Verifica quais de fato são novas tags. extract() tira só um campo
            foreach($query->extract('title') as $existing){
                $index = array_search($existing, $newTags);
                if($index !== false){
                    //Remove das novas;
                    
                    unset($newTags[$index]);
                }
            }
            
            debug($newTags);

            $out = [];

            //Coloca as que já existem;
            foreach($query as $tag){
                $out[] = $tag;
            }

            //Cria as novas e as coloca 
            foreach($newTags as $tag){
                $out[] = $this->Tags->newEntity(['title' => $tag]);
            }

            return $out;
        }


        //Esse é um método que pode ser implementado/sobreescrito e que configura as validações para cada campo.
        public function validationDefault(\Cake\Validation\Validator $validator){
            $validator
                ->allowEmpty('title', false)    
                ->minLength('title', 10)
                ->maxLength('title', 255)

                ->allowEmpty('body', false)
                ->minLength('body', 10);

            return $validator;
        }

        //Implementando o custom finder method "findTagged" que usamos no método tags do controller.
        public function findTagged(Query $query, array $options){
            //No cake os construtores são suscintos, a alguns dos metodos podem ser incluidos nos Models pois fazem mais sentido assim.
            //O parâmetro $quey é uma instancia do query builder que o cake tem.
            //O segundo é um array de opções. O parâmetro passado por nós e vêm lá do controller está em $options['tags'];
            
            $columns = [
                'Articles.id', 'Articles.user_id', 
                'Articles.title', 'Articles.body', 
                'Articles.published', 'Articles.created', 
                'Articles.slug'
            ];

            $query = $query->select($columns)->distinct($columns);

            if(empty($options['tags'])){
                //Se não tiver tags, acha os artigos sem tags
                $query->leftJoinWith('Tags')->where(['Tags.title IS' => null]);
            }else{
                //Encontra os artigos que tenham uma ou mais tags que estavam no array;
                $query->innerJoinWith('Tags')->where(['Tags.title IN' => $options['tags']]);
            }
            
            //Finder methods devem sempre retornar um query builder modificado.
            return $query->group(['Articles.id']);

            /*Explicando a query. Estamos selecionando artigos diferentes (Articles.id tá no distinct)
            onde, quando nenhuma tag é passada ele seleciona os artigos com as tags que possuem o titulo vazio;
            Quando existe tag ele procura os artigos diferentes uns dos outros que 
            possuem uma ou mais tags informadas no array
            */

        }
    }
?>