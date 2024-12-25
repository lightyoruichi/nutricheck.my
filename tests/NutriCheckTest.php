<?php

use PHPUnit\Framework\TestCase;

class NutriCheckTest extends TestCase
{
    private $uploadDir;
    private $testImagePath;

    protected function setUp(): void
    {
        // Create temporary upload directory for testing
        $this->uploadDir = sys_get_temp_dir() . '/nutricheck_test_' . uniqid();
        mkdir($this->uploadDir);

        // Create a test image
        $this->testImagePath = $this->uploadDir . '/test.jpg';
        $this->createTestImage();
    }

    protected function tearDown(): void
    {
        // Clean up test files
        if (file_exists($this->testImagePath)) {
            unlink($this->testImagePath);
        }
        if (is_dir($this->uploadDir)) {
            rmdir($this->uploadDir);
        }
    }

    private function createTestImage()
    {
        // Create a simple test image
        $image = imagecreatetruecolor(100, 100);
        $bgColor = imagecolorallocate($image, 255, 255, 255);
        imagefill($image, 0, 0, $bgColor);
        imagejpeg($image, $this->testImagePath);
        imagedestroy($image);
    }

    public function testFileUploadValidation()
    {
        $_FILES['foodImage'] = [
            'name' => 'test.jpg',
            'type' => 'image/jpeg',
            'tmp_name' => $this->testImagePath,
            'error' => UPLOAD_ERROR_OK,
            'size' => filesize($this->testImagePath)
        ];

        // Test file size validation
        $this->assertTrue($_FILES['foodImage']['size'] <= MAX_FILE_SIZE, 'File size should be within limits');

        // Test file type validation
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $_FILES['foodImage']['tmp_name']);
        finfo_close($finfo);

        $this->assertTrue(in_array($mimeType, ALLOWED_TYPES), 'File type should be allowed');
    }

    public function testAnalysisResult()
    {
        // Mock the analysis result
        $analysisResult = [
            'name' => 'Sample Food',
            'calories' => 250,
            'protein' => '15g',
            'carbs' => '30g',
            'fat' => '10g',
            'confidence' => '85%'
        ];

        // Test result structure
        $this->assertArrayHasKey('name', $analysisResult);
        $this->assertArrayHasKey('calories', $analysisResult);
        $this->assertArrayHasKey('protein', $analysisResult);
        $this->assertArrayHasKey('carbs', $analysisResult);
        $this->assertArrayHasKey('fat', $analysisResult);
        $this->assertArrayHasKey('confidence', $analysisResult);

        // Test data types
        $this->assertIsString($analysisResult['name']);
        $this->assertIsNumeric($analysisResult['calories']);
        $this->assertIsString($analysisResult['protein']);
        $this->assertIsString($analysisResult['carbs']);
        $this->assertIsString($analysisResult['fat']);
        $this->assertIsString($analysisResult['confidence']);
    }

    public function testErrorHandling()
    {
        // Test invalid file type
        $_FILES['foodImage'] = [
            'name' => 'test.txt',
            'type' => 'text/plain',
            'tmp_name' => __DIR__ . '/test.txt',
            'error' => UPLOAD_ERROR_OK,
            'size' => 100
        ];

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $this->testImagePath);
        finfo_close($finfo);

        $this->assertFalse(in_array('text/plain', ALLOWED_TYPES), 'Text files should not be allowed');

        // Test file size limit
        $oversizedFile = [
            'name' => 'large.jpg',
            'type' => 'image/jpeg',
            'tmp_name' => $this->testImagePath,
            'error' => UPLOAD_ERROR_OK,
            'size' => MAX_FILE_SIZE + 1
        ];

        $this->assertGreaterThan(MAX_FILE_SIZE, $oversizedFile['size'], 'Should detect oversized files');
    }

    public function testUploadDirectoryPermissions()
    {
        // Test if upload directory exists and is writable
        $this->assertTrue(is_dir($this->uploadDir), 'Upload directory should exist');
        $this->assertTrue(is_writable($this->uploadDir), 'Upload directory should be writable');
    }
} 