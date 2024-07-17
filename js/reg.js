function passwordValid () {
    const passwordInput = document.getElementById('reg__password_1');
    const password8Sim = document.getElementById('reg__8-sim');
    const passwordNum = document.getElementById('reg__num');
    const passwordUniqSim = document.getElementById('reg__!?');

    
    passwordInput.addEventListener('input', () => {
        if (passwordInput.value.length > 7) {
            password8Sim.classList.add('done');
        } else {
            password8Sim.classList.remove('done');
        }
        
        if (/[0-9]/.test(passwordInput.value)) {
            passwordNum.classList.add('done');
        } else {
            passwordNum.classList.remove('done');
        }

        if (/[!?]/.test(passwordInput.value)) {
            passwordUniqSim.classList.add('done');
        } else {
            passwordUniqSim.classList.remove('done');
        }
    })
}

passwordValid();
