<h1>Articles</h1>
<?= $this->Html->link('Add Article', ['action' => 'add']) ?>
<table>
    <tr>
        <th>Title</th>
        <th>Created</th>
        <th>Action</th>
    </tr>

    <!-- No controller existe um set para $articles, basta iterar sobre ele-->
    <?php foreach($articles as $article){?>
        <tr>
            <td>
                <!-- Evalua o php, o mesmo que echo $this... -->
                <?= $this->Html->link($article->title, ['action' => 'view', $article->slug]) ?>
            </td>
            <td>
                <?= $article->created->format(DATE_RFC850) ?>
            </td>
            <td>
                <?= $this->Html->link('Edit', ['action' => 'edit', $article->slug]) ?>
                |
                <?= $this->Form->postLink('Delete', ['action' => 'delete', $article->slug], ['confirm' => 'Are you sure?']) ?>
            </td>
        </tr>
    <?php } ?>
</table>