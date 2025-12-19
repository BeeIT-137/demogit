// Auto slug cho form admin (danh mục + sản phẩm)
function slugify(str) {
    str = str.toLowerCase();

    const from = "àáạảãâầấậẩẫăằắặẳẵèéẹẻẽêềếệểễìíịỉĩòóọỏõôồốộổỗơờớợởỡùúụủũưừứựửữỳýỵỷỹđ";
    const to   = "aaaaaaaaaaaaaaaaaeeeeeeeeeeeiiiiiooooooooooooooooouuuuuuuuuuuyyyyyd";

    for (let i = 0; i < from.length; i++) {
        str = str.replace(new RegExp(from[i], "g"), to[i]);
    }

    str = str.replace(/[^a-z0-9\s-]/g, '');
    str = str.replace(/\s+/g, '-');
    str = str.replace(/-+/g, '-');
    str = str.replace(/^-|-$/g, '');
    return str;
}

document.addEventListener('DOMContentLoaded', function () {
    const nameInput = document.getElementById('nameInput');
    const slugInput = document.getElementById('slugInput');

    if (nameInput && slugInput) {
        nameInput.addEventListener('input', function () {
            if (!slugInput.dataset.touched) {
                slugInput.value = slugify(nameInput.value);
            }
        });

        slugInput.addEventListener('input', function () {
            slugInput.dataset.touched = '1';
        });
    }
});
