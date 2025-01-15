<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Upload and Download</title>
<style>
        body {
            font-family: Arial, sans-serif;
        }
        .container {
            width: 50%;
            margin: 50px auto;
            text-align: center;
        }
        progress {
            width: 100%;
            height: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Upload Your File</h1>
        <input type="file" id="fileInput" />
        <button id="uploadBtn">Upload</button>
        <progress id="progressBar" value="0" max="100"></progress>
        <p id="statusMessage"></p>
    </div>

    <script>
        const CHUNK_SIZE = 5 * 1024 * 1024; // 5MB chunks
        const uploadUrl = "upload.php"; // Path to your upload handler

        document.getElementById("uploadBtn").addEventListener("click", async () => {
            const fileInput = document.getElementById("fileInput");
            const progressBar = document.getElementById("progressBar");
            const statusMessage = document.getElementById("statusMessage");

            if (!fileInput.files.length) {
                statusMessage.textContent = "Please select a file.";
                return;
            }

            const file = fileInput.files[0];
            const totalChunks = Math.ceil(file.size / CHUNK_SIZE);
            let uploadedChunks = 0;

            progressBar.value = 0;
            statusMessage.textContent = "Uploading...";

            for (let start = 0; start < file.size; start += CHUNK_SIZE) {
                const chunk = file.slice(start, start + CHUNK_SIZE);
                const formData = new FormData();
                formData.append("chunk", chunk);
                formData.append("fileName", file.name);
                formData.append("chunkIndex", uploadedChunks);
                formData.append("totalChunks", totalChunks);

                try {
                    const response = await fetch(uploadUrl, {
                        method: "POST",
                        body: formData,
                    });

                    const result = await response.json();
                    if (result.status === "error") {
                        throw new Error(result.message);
                    }

                    uploadedChunks++;
                    progressBar.value = (uploadedChunks / totalChunks) * 100;

                    if (uploadedChunks === totalChunks) {
                        statusMessage.textContent = "Upload complete!";
                    }
                } catch (error) {
                    statusMessage.textContent = `Error: ${error.message}`;
                    break;
                }
            }
        });
    </script>
    <h2>Available Files:</h2>
    <ul>
        <?php
        // List uploaded files
        $files = array_diff(scandir('uploads'), array('.', '..'));
	
        foreach ($files as $file) {
        	echo "<li><a href='download.php?file=" . urlencode($file) . "'>" . htmlspecialchars($file) . "</a></li>";
	    
        }
        ?>
    </ul>
</body>
</html>
