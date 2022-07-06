jQuery(document).on('click', '.ginput_container_fileupload', function () {
    this.parentElement.querySelector('label').click();
});
jQuery(document).on('change', '[type="file"]', function () {
    readURL(this);
});

// document.querySelector('[type="file"]').addEventListener('change', function (e) {
//     readURL(this);
// });

function readURL(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            let preview = input.parentElement.querySelector('.preview');
            if (preview) {
                preview.src=e.target.result;
            } else {
                let img = document.createElement('img');
                img.classList = 'preview';
                img.src = e.target.result;
                input.parentElement.appendChild(img);
                let style = document.createElement("style");
                let main_id = input.parentElement.parentElement.id;
                style.innerHTML = "#" + main_id + " .ginput_container_fileupload:after { content: initial}";
                input.parentElement.appendChild(style);
            }
        }

        reader.readAsDataURL(input.files[0]);
    }
}
jQuery('body').on('click', '#gform_next_button_1_12', function () {
    setTimeout(() => {
        let state=null;
let city=new TomSelect("#input_1_25_4",{
	persist: true,
    create: false,
    onFocus: function () {
        city.clear();
    },
    onDropdownOpen: function () {
        city.clear();
    },
    onChange: function () {
        if (state) {
            state.destroy();
        }

        setTimeout(() => {
        state=new TomSelect("#input_1_25_3",{
            persist: false,
            selectOnTab:true,
            create: false,
            maxItems: 1,
            maxOptions:5,
            onFocus: function () {
                state.clear();
            },
            onDropdownOpen: function () {
                state.clear();
            },
        });
        }, 300);
    },
    onInitialize: function () {
        setTimeout(() => {
            state=new TomSelect("#input_1_25_3",{
                persist: false,
                selectOnTab:true,
                create: false,
                maxItems: 1,
                maxOptions:5,
                onFocus: function () {
                    state.clear();
                },
                onDropdownOpen: function () {
                    state.clear();
                },
            });
            }, 1000);
    }
});

    }, 1000);
})


