<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dummy variables creator</title>
</head>
<body>
    <form action="/public/index.php/upload" method="post" enctype="multipart/form-data">
        <input type="file" name="file" />
        <br>
        <label>Column Id</label>
        <input type="text" name="columnId[]" />
        <br>
        <label>Column Id</label>
        <input type="text" name="columnId[]" />
        <br>
        <button type="submit">Upload</button>
    </form>
</body>
</html>