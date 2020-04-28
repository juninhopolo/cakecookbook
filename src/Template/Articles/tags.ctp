<!-- 
/*Caminho percorrido pela requisição;
A requisição bate na nossa rota customizada /tagged/*;
a rota encaminha para o controller Article e pra action tag();
a action chama o find(customizado) findTagged. Todo find retorna
um query builder modificado e então esse query builder retorna dados
e chama o template com o mesmo nome da action
*/
-->

<h1>
    Articles tagged with: 
    <?= $this->Text->toList(h($tags), 'or') ?> 
</h1>

<section>
    <?php foreach($articles as $article){ ?>
        <article>
            <h4><?= $this->Html->link($article->title, ['controller' => 'Articles', 'action' => 'view', $article->slug]) ?></h4>
            <span><?= h($article->created) ?></span>
        </article>
    <?php } ?>
</section>