<?php
    require_once "DocumentChunkService.php";
    require_once "../../utils/utils.php";
    $pdfText = extractPdfText("../../pdf/test.pdf");

    create_chunk($pdfText, null);