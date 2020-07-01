<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
<script>
    axios.post('/refresh', {
        params: {
            refresh_token: "def5020001fe047b0685143f96cf6e82528a86f18e2cb4779072a7322a7275abe7488c2d7065b7c2abebd591e9b157ef2ebd3b191c2d9af3023397517c7caa6940ac967e7201a658f4057bedf890d0f1e2a03c614d53a0a5a8f99bd5a5f061f558e2870ba49255f28e5bf17eb60ca6f1b37046780e76c99dec51d2d4f0cf49b2bac37b3e6db3fe7428cd3e4ff012da24f735f7823b43dff475d235b49d42b7f0f1e0b78e360b5eb81ff2b07dd7537e4af1004c4d52f9e5eca0b05fb9e3757964e9b73ad3f595128b4c89795829cc2a5ab8bf75fd95f3e3fa199e0cdac6489384903fd532d0f303ad512b26f8cad8bfa719d931e46f66dbec156eb3db2ebfc7564540ee0c6cebf2350b10b117f37bf6fa9b6f5a87bda29d5968f17b4a7e9c22e3eb9dc9b9f9644d0681f696c82b911389eb46b3635ffbcd803062c2daa18fd98d497a15e34073ba428ada8be892eee2a7778569ee635865ee791c657287777aeb1d"
        }
    })
        .then(function (response) {
            console.log(response.data);
        });
</script>
