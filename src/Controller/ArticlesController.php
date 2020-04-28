<?php

    namespace App\Controller;

    class ArticlesController extends AppController{
        public function initialize(){
            parent::initialize();

            $this->loadComponent('Paginator');
            $this->loadComponent('Flash');
        }

        public function index(){
            $articles = $this->Paginator->paginate($this->Articles->find());


            $this->set(compact('articles'));
        }

        public function view($slug = null){
            //findBySlug é um DynamicFinder criado automaticamento com base no DB;
            //Como articles possui uma relação belongsTooMany é preciso usar contain para que as tags sejam carregadas também
            $article = $this->Articles->findBySlug($slug)->contain(['Tags'])->firstOrFail();

            //O mesmo que compact($article);
            $this->set(compact('article'));
        }

        public function add(){
            //Cria um novo objeto vazio de Artigo;
            $article = $this->Articles->newEntity();
            if ($this->request->is('post')) {
                //Vincula os dados da requisição ao objeto;
                $article = $this->Articles->patchEntity($article, $this->request->getData());

                //Altera/trabralha em cima dos dados;
                $article->user_id = 1;

                //Executa o processo de salvar no banco. Tenta enviar a entidade (Entity) para o Table, que então persiste. 
                if($this->Articles->save($article)){
                    //Se ok mostra mensagem de sucesso e redireciona para outra rota.
                    $this->Flash->success(__('Your article has been saved!'));
                    return $this->redirect(['action' => 'index']);
                }

                //Se não der certo apenas mostra um erro e permanece na página, desse modo dá para mostrar erros de validação.
                $this->Flash->error(__('Unable to add your article.'));
            }   
            
            /*Como dissemos no "initialize" que um artigo pertence a muitas tags
                aqui informamos ao controller para expor as tags disponiveis.
                Os código list carrega um array de id => title que vai permitir criar uma selection list;
            */
            $tags = $this->Articles->Tags->find('list');
            
            $this->set('tags', $tags);

            $this->set('article', $article);
        }

        //Como slug é chave única, podemos usar ele para identificar um registro que queremos editar;
        public function edit($slug){
            //Aqui, o método "contains" carrega as tags que estão associadas ao artigo;
            $article = $this->Articles->findBySlug($slug)->contain(['Tags'])->firstOrFail();
            if($this->request->is(['post', 'put'])){
                $this->Articles->patchEntity($article, $this->request->getData());

                if($this->Articles->save($article)){
                    $this->Flash->success(__('This article was updated!'));
                    return $this->redirect(['action' => 'index']);
                }

                $this->Flash->error(__('Unable to update this article'));
            }

            //Como o usuário pode adicionar ou remover tags, é preciso expor as tags ao template;
            $tags = $this->Articles->Tags->find('list');
            $this->set('tags', $tags);

            $this->set('article', $article);
        }

        public function delete($slug){
            //Define os protocolos que a rota aceita;
            $this->request->allowMethod(['post', 'delete']);

            $article = $this->Articles->findBySlug($slug)->firstOrFail();
            
            if($this->Articles->delete($article)){
                $this->Flash->success(__('The article: "{0}" was successfuly deleted.', $article->title));
                return $this->redirect(['action' => 'index']);
            }
            
            $this->Flash->error(__('Unable to delete this article'));
        }

        public function tags(){
            //'pass' é do cake e contém os URL PARAMS da requisição feita;
            $tags = $this->request->getParam('pass');

            //tagged se refere a uma método do nosso model que encontra por tag
            //porém essa não é gerado pelo cake automáticamente pois isso é muito
            //específico, portanto implementaremos. O chamados "custom finder methods".
            $articles = $this->Articles->find('tagged', ['tags' => $tags]);

            $this->set(['articles' => $articles, 'tags' => $tags]);
        }
    }
?>