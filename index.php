<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>IEC modul</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <style>

    </style>

</head>
  <body>

    <div class="jumbotron text-center bg-secondary text-white">
        <h1>IEC modul</h1>
        <p>teszt verzió</p>
    </div>


<ul class="nav nav-pills">
  <li class="nav-item">
    <a id="menuhome"    logout  class="nav-link" onclick="setstatus(status)">Főoldal</a>
  </li>
  <li class="nav-item">
    <a id="menulogin"   logout  class="nav-link" onclick="setstatus('underlogin')" >Login</a>
  </li>
  <li class="nav-item">
    <a id="menureg"     logout  class="nav-link" onclick="setstatus('underreg')">Regisztráció</a>
  </li>
  <li class="nav-item">
    <a id="konyvlista"  loggedin class="nav-link" style="display: none;" onclick="booklist()">Könyvlista</a>
  </li>
  <li class="nav-item">
    <a id="classpelda"  loggedin class="nav-link" style="display: none;" onclick="classpelda()">Osztálypélda</a>
  </li>  
  <li class="nav-item">
    <a id="menulogout"  loggedin class="nav-link" style="display: none;" onclick="logout()">Kilépés</a>
  </li>
</ul>


<div id="reg" underreg class="container" style="display:none;">
    <h2>Regisztráció</h2>
    <form>
        Felhasználónév: <input type="text" id="regusername"><br>
        Jelszó: <input type="password" id="regpassword1"><br>
        Jelszó újra: <input type="password" id="regpassword2"><br>
        <button type="button" onclick="reg()">Regisztrálás</button>
        <button type="button" onclick="setstatus('logout')">Mégsem</button>
    </form>
    <div id="reginfo"></div>
</div>

<div id="login" underlogin class="container" style="display:none;">
    <h2>Bejelentkezés</h2>
    <form>
        Felhasználónév: <input type="text" id="loginusername"><br>
        Jelszó: <input type="password" id="loginpassword"><br>
        <button type="button" onclick="login()" >Bejelentkezés</button>
        <button type="button" onclick="setstatus('logout')">Mégsem</button>
    </form>
    <div id="logininfo"></div>
</div>

<!-- Base content -->
<div id="home" logout class="container">
    <div class="col-3 center">
        <img src="images/school.webp"/>
    </div>
</div>

<!-- Rejtett tartalom -->
<div id="content" loggedin class="container" style="display: none;">   
    <h2>Lista</h2>
</div>




<script>
    var status="logout"; // logout, underreg, underlogin, loggedin

    function $(name){
        return document.getElementById(name);
    }
    /* TEST:
        let a = $("almaid");
    */
    function $$(name,attrname,value){
        document.getElementById(name).setAttribute(attrname,value);
    }
    /* TEST:
        $$("almaid","class","container center") 
        $$("almaid","name","berhenye") 
     */

    function setstatus( stat ){
        status = stat;
        refreshElements();
    }


    function refreshElements(){
        const elements = document.querySelectorAll('[logout],[underreg],[underlogin],[loggedin]');

        elements.forEach( element => {
            
            if (element.hasAttribute('underlogin') && status=='underlogin' ) {
                element.style.display='block';
            } else if (element.hasAttribute("underreg") && status=="underreg" ) {
                element.style.display='block';
            } else if (element.hasAttribute("loggedin") && status=="loggedin" ) {
                element.style.display='block';
            } else if (element.hasAttribute("logout") && status=="logout" ) {
                element.style.display='block';
            } else {
                element.style.display="none";
            }

        } 
        );

    }

    window.onload = function(){
        refreshElements();
    }


    /* -------------------------------- */

    async function login(){
        const username = $('loginusername');
        const password = $('loginpassword');

        let responsejson = await runAuth('login',username.value,password.value);
        let response = JSON.parse(responsejson); 

        if ( response.success ) {
            setstatus('loggedin');
            refreshElements();
        } else  {
            alert('Nincs ilyen felhasználó!');
        }
        
    }

    async function logout(){
        let responsejson = await runAuth('logout','','');
        let response = JSON.parse(responsejson); 

        if ( response.success ) {
            setstatus('logout');
            refreshElements();
        } else  {
            alert('Nem tudott kilépni!');
        }
        
    }


    async function reg(){
        const username = $('regusername');
        const pwd1 = $('regpassword1');
        const pwd2 = $('regpassword2');

        if (pwd1.value != pwd2.value) {
            alert("A jelszavak nem egyeznek")
            return
        }
        // további megkötések a jelszóra: hossza minimum, tartalma minimum 

        let responsejson = await runAuth('reg',username.value,pwd1.value);
        let response = JSON.parse(responsejson); 

        if ( response.success ) {
            setstatus('loggedin');
            refreshElements();
        } else  {
            alert('Már van ilyen regisztráció!');
        }
        
    }

    // command = login , reg, logout
    async function runAuth( command, username='',password='' ) {
        const response = await fetch('php/auth.php',  
        {
            method:'POST',
            headers: { 'Content-Type':'application/x-www-form-urlencoded' },
            body: 'cmd=' +command+'&username='+username+'&password='+password
        }
        );
        return await response.text();
    }

    // BOOKLIST

    async function runDB( command ) {
        const response = await fetch('php/db.php',  
        {
            method:'POST',
            headers: { 'Content-Type':'application/x-www-form-urlencoded' },
            body: 'cmd=' +command
        }
        );
        return await response.text();
    }

    async function booklist(){
        let responsejson = await runDB('booklist');
        let response = JSON.parse(responsejson); 

        if ( response.konyvek ) {
            displayTable( response.konyvek );  
        } else  {
            alert('Nincs jogosultsága listázni!');
        }        
    }

    function displayTable( booklist ){
        div = $('content');
        let s = '';
        booklist.forEach( row => {
            s = s + row['konyvcim'] + '<br>';
        });
        div.innerHTML = s;
    }


    //CLASSPELDA

    class TMotor {
        #tipus;
        constructor(tipus){
            this.#tipus=tipus;
        }
        gettipus(){
            return this.#tipus;
        }
    }

    class TAuto {
        marka;
        modell;
        motor;
        constructor(marka,modell,motortipus){
            this.marka=marka;
            this.modell=modell;
            this.motor = new TMotor(motortipus);     
        }

        leiras() {
            return `${this.marka} ${this.modell} Motor típusa: ${this.motor.gettipus()}`; 
            //return this.marka + ' ' + this.modell + ' Motor típusa: ' + this.motor.gettipus();
        }
    }

    class TAutopark {
        autok = [];

        autoadd(auto){
            this.autok.push(auto);
        }

        listaz(){
            return this.autok.map(  auto => auto.leiras()   );
        }
    }


    function classpelda(){
        const autopark = new TAutopark();

        autopark.autoadd( new TAuto('Audi','A4','benzin') );
        autopark.autoadd( new TAuto('Toyota','Corolla','benzin') );
        autopark.autoadd( new TAuto('Ford','Focus','dízel') );
        autopark.autoadd( new TAuto('Tesla','Model S','elektromos') );
        autopark.autoadd( new TAuto('BMW','320i','benzin') );
        autopark.autoadd( new TAuto('Hyundai','Ioniq','elektromos') );
        autopark.autoadd( new TAuto('Nissan','Leaf','elektromos') );

        let elem = document.getElementById("content");
        elem.innerText='';
        t = autopark.listaz(); 

        t.forEach( leiras => {
            const li = document.createElement('li');
            li.textContent = leiras;
            elem.appendChild(li);
        } );


    }





</script>




    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  </body>
</html>