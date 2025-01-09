<?php 

function validateAndSaveImage($base64Data, $save_image = true) {
    // Decode base64 data
    $imageData = base64_decode($base64Data);
    if ($imageData === false) {
        return ['success' => false, 'error' => 'Invalid base64 data'];
    }

    // Check file size (1MB = 1048576 bytes)
    if (strlen($imageData) > 1048576) {
        return ['success' => false, 'error' => 'File size exceeds 1MB limit'];
    }

    // Create a temporary file to check mime type
    $tempFile = tmpfile();
    $tempFilePath = stream_get_meta_data($tempFile)['uri'];
    file_put_contents($tempFilePath, $imageData);

    // Check if it's really an image and get mime type
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($tempFilePath);
    
    // List of allowed mime types
    $allowedTypes = [
        'image/jpeg',
        'image/jpg',
        'image/png'
    ];

    if (!in_array($mimeType, $allowedTypes)) {
        fclose($tempFile);
        return ['success' => false, 'error' => 'Invalid file type. Only JPG, JPEG, and PNG allowed'];
    }

    // Get file extension from mime type
    $extensions = [
        'image/jpeg' => 'jpg',
        'image/jpg' => 'jpg',
        'image/png' => 'png'
    ];
    
    $extension = $extensions[$mimeType];
    
    // Generate safe filename
    $newFilename = uniqid('image_', true) . '.' . $extension;
    $uploadPath = ROOT_PATH . '/public_html/uploads/' . $newFilename;

    // Additional security check - verify image integrity
    if (!getimagesize($tempFilePath)) {
        fclose($tempFile);
        return ['success' => false, 'error' => 'Invalid image file'];
    }

    // Save the file using file_put_contents instead of move_uploaded_file
    try {
        // Make sure upload directory exists
        if (!is_dir(dirname($uploadPath))) {
            mkdir(dirname($uploadPath), 0755, true);
        }
        
        // Check if directory is writable
        if (!is_writable(dirname($uploadPath))) {
            throw new Exception('Upload directory is not writable');
        }
        
        if($save_image === true){
            // Save the file
            if (file_put_contents($uploadPath, $imageData) === false) {
                throw new Exception('Failed to write file to disk');
            }
            // Set proper file permissions
            chmod($uploadPath, 0644);
        }
        
        
    } catch (Exception $e) {
        fclose($tempFile);
        return ['success' => false, 'error' => 'Failed to save file: ' . $e->getMessage()];
    }

    // Clean up
    fclose($tempFile);

    return [
        'success' => true,
        'filename' => $newFilename,
        'path' => $uploadPath
    ];
}
