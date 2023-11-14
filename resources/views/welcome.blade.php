<html>
<head>
    <title>Импорт пользователей</title>
</head>

<body>


<button type="button" id="get">импортировать пользователей</button>
<div id="result"></div>
<script>
    function getData() {

        const requestURL = '/public/api/getusers';
        const xhr = new XMLHttpRequest();
        xhr.open('GET', requestURL);
        xhr.onload = () => {
            if (xhr.status !== 200) {
                return;
            }
            document.querySelector('#result').innerHTML = xhr.response;
        }
        xhr.send();
    }

    document.querySelector('#get').addEventListener('click', () => {
        getData();
    });
</script>

</body>
</html>



