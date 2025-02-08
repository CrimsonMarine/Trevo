document.addEventListener('DOMContentLoaded', function() {
    var input50 = document.getElementById('inputTo50');
    var text50 = document.getElementById('textTo50');

    var input200 = document.getElementById('inputTo200');
    var text200 = document.getElementById('textTo200');

    function checkStrSize(input, text, midsize, maxsize) {
        if (input && text) {
            input.addEventListener('input', function() {
                var length = input.value.length;
                text.textContent = length;
                if (length >= maxsize) {
                    text.style.color = 'rgb(167, 34, 34)';
                } 
                else if (length >= midsize) {
                    text.style.color = 'rgb(167, 167, 34)'; 
                } 
                else {
                    text.style.color = '';
                }
            });

            text.textContent = input.value.length;
        }
    }

    if (input50 && text50) {
        checkStrSize(input50, text50, 30, 50);
    }

    if (input200 && text200) {
        checkStrSize(input200, text200, 100, 200);
    }
});
