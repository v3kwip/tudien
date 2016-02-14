<?php

namespace tudien\entity\term;

class Definition
{
    const TYPE_NOUN        = 100;
    const TYPE_NOUN_GENDER = 150;
    const TYPE_DETERMINER  = 200;
    const TYPE_PRONOUN     = 300;
    const TYPE_VERB        = 400;
    const TYPE_ADJECTIVE   = 500;
    const TYPE_ADVERB      = 600;
    const TYPE_PREPOSITION = 700;
    const TYPE_CONJUNCTION = 800;

    private $id;
    private $description;
}
