<?php

use PHPUnit\Framework\TestCase;

class NutriCheckTest extends TestCase
{
    private $uploadDir;

    protected function setUp(): void
    {
        $this->uploadDir = sys_get_temp_dir() . '/nutricheck_test_uploads';
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir);
        }
    }

    protected function tearDown(): void
    {
        $files = glob($this->uploadDir . '/*');
        foreach ($files as $file) {
            unlink($file);
        }
        rmdir($this->uploadDir);
    }

    public function testUploadDirectoryIsWritable()
    {
        $this->assertTrue(is_writable($this->uploadDir), 'Upload directory should be writable');
    }

    public function testFileUploadValidation()
    {
        $testFile = $this->createTestImage();
        $uploadedFile = [
            'name' => 'test.jpg',
            'type' => 'image/jpeg',
            'tmp_name' => $testFile,
            'error' => UPLOAD_ERR_OK,
            'size' => filesize($testFile)
        ];

        $this->assertTrue(
            $this->validateUpload($uploadedFile),
            'Valid image upload should pass validation'
        );
    }

    public function testInvalidFileType()
    {
        $testFile = tempnam(sys_get_temp_dir(), 'test');
        file_put_contents($testFile, 'Invalid file content');
        $uploadedFile = [
            'name' => 'test.txt',
            'type' => 'text/plain',
            'tmp_name' => $testFile,
            'error' => UPLOAD_ERR_OK,
            'size' => filesize($testFile)
        ];

        $this->assertFalse(
            $this->validateUpload($uploadedFile),
            'Invalid file type should fail validation'
        );
        unlink($testFile);
    }

    private function createTestImage()
    {
        $file = tempnam(sys_get_temp_dir(), 'test');
        $im = imagecreatetruecolor(100, 100);
        imagejpeg($im, $file);
        imagedestroy($im);
        return $file;
    }

    private function validateUpload($file)
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return false;
        }

        if ($file['size'] > 10485760) { // 10MB
            return false;
        }

        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        return in_array($mimeType, $allowedTypes, true);
    }
} 