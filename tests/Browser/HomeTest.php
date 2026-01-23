<?php


it('may welcome the user', function () {
    $page = visit(url());
    $page->screenshot();
    $page->assertSee('Welcome');
});
