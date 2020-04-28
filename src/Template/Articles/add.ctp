<h1>Add Article</h1>
<?php
    //O cake possui alguns HELPERS como o FormHelper usaado aqui;

    //O mesmo que escrever em HTML <form method='POST' action='/articles/add'>
    echo $this->Form->create($article);

    //Setando usuário manualmente, por enquanto.
    echo $this->Form->control('user_id', ['type' => 'hidden', 'value' => 1]);

    echo $this->Form->control('title');
    echo $this->Form->control('body', ['rows' => '3']);
    //echo $this->Form->control('tags._ids', ['options' => $tags]);
    echo $this->Form->control('tag_string', ['type' => 'text']);
    echo $this->Form->button(__('Save Article'));

    echo $this->Form->end();

    //A vantagem de usar FormHelper é que ele puxa informações do banco para criar os inputs;
?>