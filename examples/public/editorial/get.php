<?php
    use OAuth2ServerExamples\Repositories\ClientRepository;
    use OAuth2ServerExamples\Repositories\EditorialRepository;
    use OAuth2ServerExamples\Entities\TestimonyEntity;

    header("Access-Control-Allow-Origin: *");

    include __DIR__ . '/../../vendor/autoload.php';

    $testimonyRepo = new EditorialRepository();

    try{
        $testimonies = [];
        $test = $testimonyRepo->getTestimonies();
        foreach ($test as $key => $testimony) {
            array_unshift($testimonies, json_encode($testimony));
        }
        echo json_encode($test);

    } catch (Exception $e){
        $response = json_encode(["status" => $e->getCode(), "msg" => $e->getMessage()]);
        echo $response;
    }