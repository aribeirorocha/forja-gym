import os

def fix_file(filepath):
    with open(filepath, 'r') as f:
        content = f.read()

    # Admin.php replacement
    if 'eq_photo' in content:
        target = """if (in_array($fileExtension, ['jpg','jpeg','png','gif','webp'])) {"""
        replacement = """$mime_type = mime_content_type($_FILES['eq_photo']['tmp_name']);
                    if (in_array($fileExtension, ['jpg','jpeg','png','gif','webp']) && strpos($mime_type, 'image/') === 0) {"""
        content = content.replace(target, replacement, 1)

    if 'trainer_photo' in content:
        target = """$fileExtension = strtolower(pathinfo($_FILES['trainer_photo']['name'], PATHINFO_EXTENSION));
                        if (in_array($fileExtension, ['jpg','jpeg','png','gif','webp'])) {"""
        replacement = """$fileExtension = strtolower(pathinfo($_FILES['trainer_photo']['name'], PATHINFO_EXTENSION));
                        $mime_type = mime_content_type($_FILES['trainer_photo']['tmp_name']);
                        if (in_array($fileExtension, ['jpg','jpeg','png','gif','webp']) && strpos($mime_type, 'image/') === 0) {"""
        content = content.replace(target, replacement, 1)

    if 'nu_photo' in content:
        target = """$fileExtension = strtolower(pathinfo($_FILES['nu_photo']['name'], PATHINFO_EXTENSION));
                        if (in_array($fileExtension, ['jpg','jpeg','png','gif','webp'])) {"""
        replacement = """$fileExtension = strtolower(pathinfo($_FILES['nu_photo']['name'], PATHINFO_EXTENSION));
                        $mime_type = mime_content_type($_FILES['nu_photo']['tmp_name']);
                        if (in_array($fileExtension, ['jpg','jpeg','png','gif','webp']) && strpos($mime_type, 'image/') === 0) {"""
        content = content.replace(target, replacement, 1)

    with open(filepath, 'w') as f:
        f.write(content)

def fix_profile(filepath):
    with open(filepath, 'r') as f:
        content = f.read()
    
    target = """if (in_array($fileExtension, ['jpg','jpeg','png','gif','webp'])) {
                    $newFileName = md5(time() . $fileName) . '.' . $fileExtension;"""
    replacement = """if (in_array($fileExtension, ['jpg','jpeg','png','gif','webp'])) {
                    $mime_type = mime_content_type($fileTmpPath);
                    if (strpos($mime_type, 'image/') === 0) {
                        $newFileName = md5(time() . $fileName) . '.' . $fileExtension;"""
    
    # We also need to close the extra brace
    target2 = """$success_msg = 'Foto de perfil atualizada com sucesso.';
                    } else {
                        $error_msg = 'Erro ao guardar foto.';
                    }
                } else {
                    $error_msg = 'Formato de imagem inválido.';
                }"""
    replacement2 = """$success_msg = 'Foto de perfil atualizada com sucesso.';
                    } else {
                        $error_msg = 'Erro ao guardar foto.';
                    }
                    } else {
                        $error_msg = 'O ficheiro não é uma imagem válida.';
                    }
                } else {
                    $error_msg = 'Formato de imagem inválido.';
                }"""

    content = content.replace(target, replacement)
    content = content.replace(target2, replacement2)

    with open(filepath, 'w') as f:
        f.write(content)

fix_file('project/pages/admin.php')
fix_profile('project/pages/profile.php')
