const log = console.log;
var fileAccess = '../api/api.php';

var userForms;
var products;
var favorites;
var basket;
var contact;

class Page {
    productSelectedData;
    basketContent;
    constructor() {
        this.body = document.body;
        this.navbar = document.getElementsByTagName('nav');
        this.darkSwitchButton = document.querySelector('#darkSwitchButton');
        this.menuButtons = document.querySelectorAll('.nav-link');
        this.ifLoginMenuButtons = document.querySelectorAll('.iflogin');
        this.basketButton = document.querySelector('#basketButton');
        this.favoriteButton = document.querySelector('#favoriteButton');
        this.userButton = document.querySelector('#userButton');
        this.footerButtons = document.querySelectorAll('footer span');
        this.message = document.querySelector('#message');
        this.emailDiv = document.qu

        this.getBrowserVariables();
        this.pageAddeventListeners();
        this.themeOptions();
        this.footerLinksOptions();

        let menuLoginVariable = (localStorage.getItem('login') !== null) ?  'on' : 'off';
        this.menuLogin(menuLoginVariable);

        this.favoriteButton.addEventListener('click', () => {
            this.pageLoad('kedvencek');
        });
        this.basketButton.addEventListener('click', () => {
            this.pageLoad('kosar');
        });

        this.pageLoad(sessionStorage.getItem('inc'));
    }

    pageAddeventListeners = () => {
        this.darkSwitchButton.addEventListener('click', this.pushDarkSwitchButton);
        // menu
        this.menuButtons.forEach(button => {
            let buttonValue = button.innerHTML.toLocaleLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");

            button.addEventListener('click', () => {
                this.pageLoad(buttonValue);
            });
        });

        this.userButton.addEventListener('click', () => {
            let button;
            this.pageLoad('felhasznalo');
        });
        
        // footer buttons
        this.footerButtons.forEach(element => {
            
            if (element.id == 'footer-login') {  
                element.onclick = () => {
                    if (typeof userForms !== 'undefined' && userForms !== null) {
                        userForms.logOut();
                        page.pageLoad('felhasznalo');
                    }
                }
            }

            if (element.id == 'footer-registracion') {
                element.onclick = () => {
                    if (typeof userForms !== 'undefined' && userForms !== null) {
                        userForms.logOut();
                        localStorage.setItem('inc', 'felhasznalo');
                        userForms.regLoginSwitch('on');
                    }
                }
            }
            
            if (element.id == 'footer-logout') {
                element.onclick = () => {
                    if (typeof userForms !== 'undefined' && userForms !== null) {
                        userForms.logOut();
                        page.pageLoad('felhasznalo');
                    } 
                }
            }

            if (element.id == 'footer-products') {
                element.onclick = () => {
                    this.pageLoad('termekek');
                }
            }

            if (element.id == 'footer-contact') {
                element.onclick = () => {
                    this.pageLoad('elerhetoseg');
                }
            }   
        });
    }

    drawMenuBg = () => {
        this.menuButtons.forEach(remove => {
            remove.style.backgroundColor = '#fff0';
        });

        this.menuButtons.forEach(find => {
           if (find.innerHTML.toLocaleLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "") == sessionStorage.getItem('inc')) {
                find.style.backgroundColor = '#bbccdd30';
           } 
        });
    }

    loadUserDatas = (tokenIn) => {
        
        let userToken = { token: tokenIn }
        api('post', '?user=userdata', userToken)
        .then((userData) => {

            if (userData.status_code == 200) {
                let userDataLocal = JSON.stringify({
                    "userid": userData.response_data.userid,
                    "username": userData.response_data.username,
                    "useremail": userData.response_data.useremail,
                    "userinfo": userData.response_data.userinfo
                });

                localStorage.setItem('userdata', userDataLocal);

                if (userForms) {
                    userForms.userDatasDrawInDisplay();
                }
                
                page.menuLogin('on');

            } else {
                this.reloadPage();
            }
        });
    }

    pageLoad = (inc) => {

        inc = inc.toLocaleLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");
        sessionStorage.setItem('inc', inc);
        this.drawMenuBg();

        if (localStorage.getItem('login')) {
            this.loadUserDatas(localStorage.getItem('login'));
            this.headBasketButtonDraw();
            this.headFavoritButtonDraw();
        }

        (inc == 'termekek') ? sessionStorage.setItem('productSwitch', 'list') : false; 
        
        $(function () {
            if (inc == 'bemutatkozas') { $('#content').load('pages/_home.html'); }
            if (inc == 'termekek') { $('#content').load('pages/_products.html'); }
            if (inc == 'felhasznalo') { $('#content').load('pages/_user.html'); }
            if (inc == 'elerhetoseg') { $('#content').load('pages/_contact.html'); }
            if (inc == 'kosar') { $('#content').load('pages/_basket.html'); }
            if (inc == 'kedvencek') { $('#content').load('pages/_favorite.html'); }
        });

    }

    getBrowserVariables = () => {
        
        !sessionStorage.getItem('inc') ? sessionStorage.setItem('inc', 'bemutatkozas') : false;
        !localStorage.getItem('darkModeSwitch') ? localStorage.setItem('darkModeSwitch', 'on') : false;
        localStorage.setItem('basket', null);
        
        sessionStorage.setItem('filter-stlye', 0);
        sessionStorage.setItem('filter-name', '');
        sessionStorage.setItem('filter-min', 0);
        sessionStorage.setItem('filter-max', 0);
        sessionStorage.setItem('filter-markdown', 0);
        
        sessionStorage.setItem('user-product-piece', 0);
        sessionStorage.setItem('user-product-instock', 0);
    }
    
    themeOptions = () => {

        let useSwitch = (make, variable, value) => {
            (make == 'add') ? variable.classList.add(value) : variable.classList.remove(value);
        }

        if (localStorage.getItem('darkModeSwitch') == 'on') {
            useSwitch('remove', this.body, 'dark-mode');
            useSwitch('remove', this.navbar[0], 'navbar-dark');
            useSwitch('remove', this.navbar[0], 'bg-dark');
            useSwitch('add', this.body, 'light-mode');
            useSwitch('add', this.navbar[0], 'navbar-light');
            useSwitch('add', this.navbar[0], 'bg-light');
        } else {
            useSwitch('remove', this.body, 'light-mode');
            useSwitch('remove', this.navbar[0], 'navbar-light');
            useSwitch('remove', this.navbar[0], 'bg-light');
            useSwitch('add', this.body, 'dark-mode');
            useSwitch('add', this.navbar[0], 'navbar-dark');
            useSwitch('add', this.navbar[0], 'bg-dark');
        }

        if (localStorage.getItem('darkModeSwitch') == 'on') {            
            this.darkSwitchButton.classList.remove('modeSwitch-off');
            this.darkSwitchButton.classList.add('modeSwitch-on');
        } else {
            this.darkSwitchButton.classList.remove('modeSwitch-on');
            this.darkSwitchButton.classList.add('modeSwitch-off');
        }
    }

    pushDarkSwitchButton = () => {

        if (localStorage.getItem('darkModeSwitch') == 'on') {
            localStorage.setItem('darkModeSwitch', 'off');
            this.themeOptions();
        } else {
            localStorage.setItem('darkModeSwitch', 'on');
            this.themeOptions();
        }

        localStorage.setItem('darkModeSwitch', localStorage.getItem('darkModeSwitch'));
    }

    menuLogin = (value) => {

        let displayValue;

        if (value == 'on') {
            displayValue = 'inline';
            this.userButton.classList.add('activeUserLogin');
            this.favoriteButton.style.display = 'block';
            this.basketButton.style.display = 'block';
        } else {
            displayValue = 'none';
            this.userButton.classList.remove('activeUserLogin');
            this.favoriteButton.style.display = 'none';
            this.basketButton.style.display = 'none';
        }

        this.ifLoginMenuButtons.forEach(element => {
            element.style.display = displayValue;
        });
    }

    headFavoritButtonDraw = () => {

        let sendData = {
            token: localStorage.getItem('login'),
            function: "get"
        }

        api('post', '?user=favorites', sendData)
        .then((data) => {
            if (data.status_code == 200 || data.status_code == 201) {

                if (data.response_data) {
                    this.favoriteButton.classList.add('activeFavoriteButton');
                } else {
                    this.favoriteButton.classList.remove('activeFavoriteButton');
                }
            }
        });
    }

    headBasketButtonDraw = () => {
    
        let basketContent = JSON.parse(localStorage.getItem('basketContent'));
        
        if (document.querySelector('.activeBasketButton')) {
            document.querySelector('.activeBasketButton').innerHTML = '';
        }
        
        if (basketContent != null && basketContent.length > 0) {
            this.basketButton.classList.add('activeBasketButton');

            let thingsValue = this.generateProduct('div', {class: 'thingsValue'}, basketContent.length);

            document.querySelector('.activeBasketButton').appendChild(thingsValue);
        } else {
            this.basketButton.classList.remove('activeBasketButton');
        }
    }

    footerLinksOptions = (value) => {

        if (typeof value != 'undefined') {
            (value.footerregistration) ? document.getElementById('footer-registracion').innerHTML = value.footerregistration : false;

            if (value.footerlogout == '' || value.footerlogout == undefined) {
                document.getElementById('footer-logout').innerHTML = '';
            } else {
                document.getElementById('footer-logout').innerHTML = value.footerlogout;
            }

        } else {
            document.getElementById('footer-login').innerHTML = 'Bejelentkezés';
            document.getElementById('footer-login').onclick = () => page.pageLoad('felhasznalo');
        }
    }

    generateProduct = (tagName, attributes, value) => {
        
        let newElement = document.createElement(tagName);

        if (attributes.length != 0) {
            Object.keys(attributes).forEach(key => {
                const addAttibute = document.createAttribute(key);
                addAttibute.value = attributes[key];
                newElement.setAttributeNode(addAttibute);
            });
        }

        if (value.length != 0) {
            newElement.innerHTML = value;
        }

        return newElement;
    }

    lengthCheck = (text, value, min, max, oneLength) => {

        if (value=='' && text != 'userinfo') {
            throw '<li>A(z) '+text+' mező üres!</li>';
        }

        if (oneLength) {
            if (value.length == min) {
                return true;
            } else {
                throw '<li>'+text+' csak '+min+' karakter hosszú lehet.</li>';
            }
        } else {
            if ((value.length >= min) && (value.length <= max)) {
                return true;
            } else {
                throw '<li>'+text+' nem megfelelő hosszúságú! ('+min+'-'+max+' karakter)</li>';
            }
        }
    }

    countMarkdownPrice = (price, markdown) => {
        return price-((price/100) * markdown);
    }

    fistCharUpper = (text) => {

        text = text.toLocaleLowerCase();        
        if (text.length > 0) {
            if (text.length > 1) {
                let firstChar = text[0].toUpperCase();
                let scliceText = text.slice(1, text.length);
                text = firstChar + scliceText;
            } else {
                text = text.toUpperCase();
            }
        }
        
        return text;
    }

    reloadPage () {
        localStorage.clear();
        sessionStorage.clear();
        window.location.reload();
    }

    emailCheck = (email) => {
        var re = /^(([^<>()[\]\\.,;:\s@\']+(\.[^<>()[\]\\.,;:\s@\']+)*)|(\'.+\'))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(email);
    }

    addProductToBasket(productGroup) {
        // productGroup . pData | . pictures | . quantity
        let basketContent = JSON.parse(localStorage.getItem('basketContent'));
        
        if (basketContent != null && !basketContent.length == 0) {

            let find = false;
            basketContent.forEach(list => {
                // Felismeri hogy volt már ilyen termék a kosárban. Csak hozzáadja a termék darabszámot!
                if (list.product.id == productGroup.product.id) {
                    find = true;
                    list.quantity = Number(list.quantity) + Number(productGroup.quantity);
                    list.quantity = (list.quantity > list.product.instock ) ? list.product.instock : list.quantity;
                }
            });

            if (find == false) {
                basketContent.unshift({product: productGroup.product, pictures: productGroup.pictures, quantity: Number(productGroup.quantity) });
            }

        } else {
            basketContent = [{ product: productGroup.product, pictures: productGroup.pictures, quantity: Number(productGroup.quantity) }];
        }
        
        localStorage.setItem('basketContent', JSON.stringify(basketContent));
    }
}

class UserForms {
    ramName;
    ramEmail;
    ramInfo;
    constructor() {
        this.registrationDiv = document.querySelector('#registrationDiv');
        this.loginDiv = document.querySelector('#loginDiv');
        this.userDiv = document.querySelector('#userDiv');

        this.regUsername = document.querySelector('#reg-username');
        this.regEmail = document.querySelector('#reg-email');
        this.regPwd1 = document.querySelector('#reg-pwd1');
        this.regPwd2 = document.querySelector('#reg-pwd2');
        this.regReset = document.querySelector('#reg-reset');
        this.regSubmit = document.querySelector('#reg-submit');
        this.regMessage = document.querySelector('#reg-message');
        this.regSwitch = document.querySelector('#regSwitch');

        this.logUsernameOrEmail = document.querySelector('#log-usernameoremail');
        this.logPwd = document.querySelector('#log-pwd');
        this.logSubmit = document.querySelector('#log-submit');
        this.logMessage = document.querySelector('#log-message');
        this.logOutSubmit = document.querySelector('#logout-submit');
        this.logSwitch = document.querySelector('#logSwitch');

        this.username = document.querySelector('#username');
        this.usrEmail = document.querySelector('#usr-email');
        this.usrEmailSubmit = document.querySelector('#usr-email-submit');
        this.usrInfo = document.querySelector('#usr-info');
        this.usrInfoSubmit = document.querySelector('#usr-info-submit');
        this.usrPwd = document.querySelector('#usr-pwd');
        this.usrNewPwd = document.querySelector('#usr-newpwd');
        this.usrNewPwd2 = document.querySelector('#usr-newpwd2');
        this.usrPassModSubmit = document.querySelector('#usr-passmod-submit');
        this.usrDeleteSubmit = document.querySelector('#usr-delete-submit');
        this.usrMessage = document.querySelector('#usr-message');

        this.regReset.addEventListener('click', () => this.regResetValues());
        this.regSwitch.addEventListener('click', () => this.regLoginSwitch());
        this.logSwitch.addEventListener('click', () => this.regLoginSwitch());
        this.logSubmit.addEventListener('click', () => this.userLogin());
        this.regSubmit.addEventListener('click', () => this.userRegistration());
        this.logOutSubmit.addEventListener('click', () => this.logOut());
        
        this.usrEmailSubmit.addEventListener('click', () => this.userMod('useremail', this.usrEmail.value, 6, 20));
        this.usrInfoSubmit.addEventListener('click', () => this.userMod('userinfo', this.usrInfo.value, 0, 255));
        this.usrPassModSubmit.addEventListener('click', () => this.userPwdModOrDelete(this.usrPwd.value, this.usrNewPwd.value, this.usrNewPwd2.value, 'mod'));
        this.usrDeleteSubmit.addEventListener('click', () => this.userPwdModOrDelete(this.usrPwd.value, this.usrNewPwd.value, this.usrNewPwd2.value, 'del'));

        this.regLogDraw();
    }

    logOut = () => {
        //localStorage.removeItem('login'); //localStorage.removeitem('userdata');
        localStorage.clear();
        sessionStorage.clear();

        page.favoriteButton.classList.remove('activeFavoriteButton');
        page.basketButton.classList.remove('activeBasketButton');

        this.userDiv.style.display = 'none';
        page.headBasketButtonDraw();
        page.footerLinksOptions({'footerlogout': ''});
        page.menuLogin('off');
        this.regLogDraw();
    }

    userLogin = () => {
        let logMsg = '';

        try {
            page.lengthCheck('Felhasználónév vagy e-mail cím', this.logUsernameOrEmail.value, 6, 60);
        } catch (error) {
            logMsg += error;
        }
        try {
            page.lengthCheck('jelszó', this.logPwd.value, 6, 30);
        } catch (error) {
            logMsg += error;
        }

        if (logMsg == '') {
            
            this.logMessage.innerHTML = '';

            let sendData = {
                usernameoremail: this.logUsernameOrEmail.value,
                password: this.logPwd.value
            }

            api('post', '?user=login', sendData)
            .then((data) => {
                if (data.status_code == 200 || data.status_code == 201) {
                    
                    this.regMessage.innerHTML = '';
                    this.logMessage.innerHTML = '';
                    this.usrMessage.innerHTML = '';
                    this.usrPwd.value = '';
                    this.usrNewPwd.value = '';
                    this.usrNewPwd2.value = '';
                    
                    // Login token save
                    localStorage.setItem('login', data.response_data);
                    page.loadUserDatas(data.response_data);
                    page.headFavoritButtonDraw();
                    page.footerLinksOptions({'footerlogout': 'Kijelentkezés'});

                    this.registrationDiv.style.display = 'none';
                    this.loginDiv.style.display = 'none';
                    this.userDiv.style.display = 'block';
                    page.menuLogin('on');

                } else {
                    this.logMessage.innerHTML = data.response_data;
                }
            });

        } else {
            this.logMessage.innerHTML = '<ul>'+logMsg+'</ul>';
            this.logMessage.scrollIntoView();
        }
    }

    userRegistration = () => {
        let regMsg = '';

        try { page.lengthCheck('username', this.regUsername.value, 6, 30); } catch (error) { regMsg += error; }
        try { page.lengthCheck('jelszó', this.regPwd1.value, 6, 30); } catch (error) { regMsg += error; }
        try { page.lengthCheck('jelszó mégegyszer', this.regPwd2.value, 6, 30); } catch (error) { regMsg += error; }
        try {
            page.lengthCheck('email', this.regEmail.value, 6, 60);
            if (!(page.emailCheck(this.regEmail.value))) {
                throw '<li>Az email nem email formátum!</li>';
            }
        }
        catch (error) { regMsg += error; }
        try {
            if (!(this.regPwd1.value == this.regPwd2.value)) {
                throw '<li>A jelszavak nem egyeznek!</li>';
            }
        }
        catch (error) { regMsg += error; }

        if (regMsg == '') {

            this.regMessage.innerHTML = '';

            let sendData = {
                username: this.regUsername.value,
                email: this.regEmail.value,
                password: this.regPwd1.value,
            }

            api('post', '?user=registration', sendData)
            .then((data) => {
                if (!(data.status_code == 401)) {

                    if (data.status_code == 200 || data.status_code == 201) {
                        this.regUsername.value = '';
                        this.regEmail.value = '';
                        this.regPwd1.value = '';
                        this.regPwd2.value = '';
                    }
                    this.regMessage.innerHTML = data.response_data;
                    this.regMessage.scrollIntoView();
                }
            });

        } else {
            this.regMessage.innerHTML = '<ul>'+regMsg+'</ul>';
            this.regMessage.scrollIntoView();
        }
    }

    regLoginSwitch = (brought) => {

        if (brought) { 
            this.regLogSwitchValue = brought ;
        } else {
            this.regLogSwitchValue = (this.regLogSwitchValue == 'on') ? 'off' : 'on';
        }
        this.regLogDraw();
    }

    userDatasDrawInDisplay = () => {

        if (typeof localStorage.getItem('userdata') != 'undefined') {

            let userData = JSON.parse(localStorage.getItem('userdata'));
            if (userData) {   
                this.username.innerHTML = userData.username;
                this.usrEmail.value = userData.useremail; 
                this.usrInfo.innerHTML = userData.userinfo;
            }
        }
    }

    regLogDraw = () => {
        
        if (localStorage.getItem('login') !== null) {
            // Bejelentkezve
            this.userDatasDrawInDisplay();
            this.registrationDiv.style.display = 'none';
            this.loginDiv.style.display = 'none';
            this.userDiv.style.display = 'block';
        } else {
            // Regisztráció vagy Bejelentkezés Div megjelenítése
            this.logMessage.innerHTML = '';
            this.regMessage.innerHTML = '';
            if (this.regLogSwitchValue == 'on') {
                this.registrationDiv.style.display = 'block';
                this.loginDiv.style.display = 'none';
            } else {
                this.registrationDiv.style.display = 'none';
                this.loginDiv.style.display = 'block';
            }
        }
    }

    regResetValues = () => {
        this.regUsername.value = ''; this.regEmail.value = ''; this.regPwd1.value = '';
        this.regPwd2.value = ''; this.regReset.value = ''; this.regSubmit.value = '';
    }

    userMod = (key, value, min, max) => {
        let modMsg = '';
        let userData = JSON.parse(localStorage.getItem('userdata'));

        try {
            page.lengthCheck(key, value, min, max);
        }
        catch (error) {
            modMsg = '<ul>'+error+'</ul>';
        }

        if (key=='useremail') {
            try {
                if (value==userData.useremail) {
                    throw '<li>Ez a regisztrált email címed!</li>';
                }
            }
            catch (error) {
                modMsg += '<li>'+error+'</li>';
            }
            try {
                if (!page.emailCheck(value)) {
                    this.usrEmail.value = userData.useremail;
                    throw '<li>Az email nem email formátum!</li>';
                }
            }
            catch (error) {
                modMsg += '<li>'+error+'</li>';
            }
        }

        if (modMsg == '') {
            let sendApidata={};
            sendApidata['token'] = localStorage.getItem('login');
            sendApidata[key] = value;

            api('post', '?user=usermod', sendApidata)
            .then((data) => {
                if (!(data.status_code == 401)) {

                    page.loadUserDatas(localStorage.getItem('login'));

                    this.usrMessage.innerHTML = data.response_data;
                    this.usrMessage.scrollIntoView();
                } else {
                    this.logOut();
                }
            });
        } else {
            this.usrMessage.innerHTML = '<ul>'+modMsg+'</ul>';
            this.usrMessage.scrollIntoView();
        }
        this.userDatasDrawInDisplay();
    }

    userPwdModOrDelete = (pwd, pwdNew, pwdNew2, mod) => {
        let pwdModMsg = '';
        if (mod == 'del') {
            try {
                page.lengthCheck('jelszó', pwd, 6, 20);
            }
            catch (error) {
                pwdModMsg += '<li>'+error+'</li>';
            }

            if (pwdModMsg == '') {
                let sendApidata={};
                sendApidata['token'] = localStorage.getItem('login');
                sendApidata['password'] = pwd;

                api('post', '?user=userdelete', sendApidata)
                .then((data) => {
                    if (!(data.status_code == 401)) {
                        this.usrMessage.innerHTML = data.response_data;
                        this.usrMessage.scrollIntoView();
                        if (data.status_code == 200) {
                            this.logOut();
                        }
                    } else {
                        this.logOut();
                    }
                });

                this.usrMessage.innerHTML = '';
                this.usrMessage.scrollIntoView();
            }
        }

        if (mod == 'mod') {
            try {
                page.lengthCheck('jelszó', pwd, 6, 20);
            }
            catch (error) {
                pwdModMsg += '<li>'+error+'</li>';
            }
            try {
                page.lengthCheck('Új jelszó', pwdNew, 6, 20);
            }
            catch (error) {
                pwdModMsg += '<li>'+error+'</li>';
            }
            try {
                page.lengthCheck('Jelszó mégegyszer', pwdNew2, 6, 20);
            }
            catch (error) {
                pwdModMsg += '<li>'+error+'</li>';
            }
            try {
                if (!(pwdNew == pwdNew2)) {
                    throw '<li>A két jelszó nem egyezik meg!</li>';
                }
            }
            catch (error) {
                pwdModMsg += '<li>'+error+'</li>';
            }

            if (pwdModMsg == '') {
                let sendApidata={};
                sendApidata['token'] = localStorage.getItem('login');
                sendApidata['password'] = pwd;
                sendApidata['newpassword'] = pwdNew;

                api('post', '?user=usermod', sendApidata)
                .then((data) => {
                    if (!(data.status_code == 401)) {
                        this.usrMessage.innerHTML = data.response_data;
                        this.usrMessage.scrollIntoView();
                        if (data.status_code == 200) {
                            this.usrPwd.value = '';
                            this.usrNewPwd.value = '';
                            this.usrNewPwd2.value = '';
                        }
                    } else {
                        this.logOut();
                    }
                });

                this.usrMessage.innerHTML = '';
                this.usrMessage.scrollIntoView();
            }
        }

        if (!pwdModMsg == '') {
            this.usrMessage.innerHTML = '<ul>'+pwdModMsg+'</ul>';
            this.usrMessage.scrollIntoView();
        }

        this.userDatasDrawInDisplay();
    }
}

class Products {
    priceMinMaxData;
    constructor () {

        this.headTextButton = document.querySelector('#head-text-button');
        this.productsGridboxDiv = document.querySelector('#products-gridbox');
        this.productsLoadingDiv = document.querySelector('#products-loading');
        this.productsListDiv = document.querySelector('#products-list');
        this.productsSelectedDiv = document.querySelector('#products-selected');

        this.filterTypeIn = document.querySelector('#filter-type');
        this.filterNameIn = document.querySelector('#filter-name');
        this.filterNameButton = document.querySelector('#filter-name-button');
        this.filterMinIn = document.querySelector('#filter-min');
        this.filterMinText = document.querySelector('#filter-min-text');
        this.filterMinTextValue = document.querySelector('#filter-min-text-value');
        this.filterMaxIn = document.querySelector('#filter-max');
        this.filterMaxText = document.querySelector('#filter-max-text');
        this.filterMaxTextValue = document.querySelector('#filter-max-text-value');
        this.filterMarkdownIn = document.querySelector('#filter-markdown');
        
        this.favoriteButtonxy = document.querySelector('#favorite-buttonxy');
        this.pOriginalpriceDiv = document.querySelectorAll('.p-originalprice-div');
        this.pMarkdownpriceDiv = document.querySelectorAll('.p-markdownprice-div');
        this.pName = document.querySelector('#p-name');
        this.pType = document.querySelector('#p-type');
        this.pOriginaplpriceValue  = document.querySelector('#p-originalprice-value');
        this.pMarkdownPriceValue = document.querySelector('#p-markdownprice-value');
        this.pDescriptionValue  = document.querySelector('#p-description-value');
        this.pbPrice = document.querySelector('#pb-price');
        this.pbInstock = document.querySelector('#pb-instock');
        this.productSelectMarkdown = document.querySelector('#product-select-markdown');

        this.bmbMinusValue = document.querySelector('#bmb-minus-value');
        this.bmbPlusValue = document.querySelector('#bmb-plus-value');
        this.bmbInputValue = document.querySelector('#bmb-input-value');
        this.bmbSubmitButton = document.querySelector('#bmb-submit');

        this.prodPicturesContainer = document.querySelector('#prod-pictures-container');
        this.bottomJump = document.querySelector('#bottom-jump');

        !sessionStorage.getItem('productSwitch') ? sessionStorage.setItem('productSwitch', 'list') : false;

        this.filterAddEventListeners();
        this.selectProductAddEventListeners();
        this.loadPriceMinMaxData();
    }

    insertProductTypes = () => {
        
        this.filterTypeIn.innerHTML = '';

        let selectElement = document.createElement('select');
        selectElement.setAttribute('class', 'form-control filter-select rounded');
        selectElement.setAttribute('id', 'filter-typevalue');
        
        let optionElement = [];
        
        api('get', '?product=producttypelist', '')
        .then((producttypelist) => {

            let typeList = producttypelist.response_data;

            typeList.unshift({id: 0, typename: 'összes'});

            let upper = 0;
            typeList.forEach(element => {
                optionElement[upper] = document.createElement('option');
                optionElement[upper].setAttribute('value', element.id);

                if (sessionStorage.getItem('filter-stlye') == element.id) {
                    optionElement[upper].setAttribute('selected', '');
                }

                optionElement[upper].innerHTML = element.typename;
                selectElement.appendChild(optionElement[upper]);
                upper++;
            });
            
            this.filterTypeIn.appendChild(selectElement);
        });
    }

    rangeMinMaxOptions = () => {
        
        this.priceMinMaxData.forEach(type => {

            if (type.typeid == sessionStorage.getItem('filter-stlye')) {
                this.filterMinIn.min = type.min;
                this.filterMaxIn.min = type.min;

                this.filterMinIn.max = type.max;
                this.filterMaxIn.max = type.max;

                //value !
                sessionStorage.setItem('filter-min', type.min);
                sessionStorage.setItem('filter-max', type.max);
            }
        });

        this.filterMinIn.value = sessionStorage.getItem('filter-min');
        this.filterMaxIn.value = sessionStorage.getItem('filter-max');
        this.filterMinText.innerHTML = this.filterMinIn.value+' Ft';
        this.filterMaxText.innerHTML = this.filterMaxIn.value+' Ft';
    }

    loadPriceMinMaxData = () => {

        api('get', '?product=priceminmax', '')
        .then((priceminmax) => {
            if (priceminmax.status_code == 200) {

                this.priceMinMaxData = priceminmax.response_data;
                this.rangeMinMaxOptions();
                
                this.productsBasic(); // MinMax betöltés után mehet tovább!!!
            }
        });
    }

    productsBasic = () => {
        // start
        this.filterMinTextValue.innerHTML = sessionStorage.getItem('filter-min')+' Ft';
        this.filterMaxTextValue.innerHTML = sessionStorage.getItem('filter-max')+' Ft';

        if (sessionStorage.getItem('filter-markdown') == 1) {
            this.filterMarkdownIn.checked = "checked";
        }

        this.filterNameIn.value = sessionStorage.getItem('filter-name');

        if (document.querySelector('.products-gridbox-message')) {
            document.querySelector('.products-gridbox-message').remove();
        }
        // list or selected product
        (sessionStorage.getItem('productSwitch') == 'list') ? this.productsList() : this.productSelected();
    }

    productSelected = () => {

        this.productsLoadingDiv.style.display = 'none';

        let pData = page.productSelectedData.product;
        let pPictures = page.productSelectedData.pictures;

        // favorite product
        if (localStorage.getItem('login')) {
            
            this.productSelectedFavoriteLoad(pData.id);
            this.favoriteButtonxy.addEventListener('click', () => {
                let sendData = {
                    token: localStorage.getItem('login'),
                    productid: pData.id,
                    function: "switch"
                }
                
                api('post', '?user=favorites', sendData)
                .then((data) => {
                    if (data.status_code == 200 || data.status_code == 201) {
                        this.productSelectedFavoriteLoad(pData.id);
                        page.headFavoritButtonDraw();
                    } 
                });
            });
        }

        this.favoriteButtonxy.style.display = (localStorage.getItem('login')) ? 'block' : 'none';

        // product properties
        sessionStorage.setItem('user-product-instock', pData.instock);
        sessionStorage.setItem('user-product-piece', 0);

        let realPrice = pData.price;
        this.pOriginaplpriceValue.innerHTML = realPrice+' Ft';
        
        if (pData.markdown == "0") {
            this.productSelectMarkdown.style.display = 'none';
            this.pMarkdownpriceDiv[0].style.display = 'none';
            this.pMarkdownpriceDiv[1].style.display = 'none';
            this.pbPrice.innerHTML = realPrice+' Ft';
        } else {
            this.productSelectMarkdown.innerHTML = pData.markdown+'%';
            this.productSelectMarkdown.style.display = 'flex';
            let markdownPrice = page.countMarkdownPrice(pData.price, pData.markdown);
            this.pbPrice.innerHTML = markdownPrice+' Ft';
            this.pMarkdownPriceValue.innerHTML = '<strong>'+markdownPrice+' Ft</stong>';
            this.pMarkdownpriceDiv[0].style.display = 'block';
            this.pMarkdownpriceDiv[1].style.display = 'block';
        }

        this.pName.innerHTML = pData.name;
        this.pDescriptionValue.innerHTML = pData.text;
        this.pType.innerHTML = pData.typename;
        this.pbInstock.innerHTML = 'Készleten van: <strong>'+pData.instock+' db</strong>';
        this.bmbInputValue.innerHTML = sessionStorage.getItem('user-product-piece');

        if (localStorage.getItem('login')) {
                        
            let basketButton = page.generateProduct('button', {'class': 'btn btn-warning submit-button', 'arial-disabled': 'true', 'id': 'prd-btn' }, 'KOSÁRBA');
            let basketIcon = page.generateProduct('span', { 'class': 'button-ico bi-basket' }, '');
            basketButton.appendChild(basketIcon);
            this.bmbSubmitButton.appendChild(basketButton);
            this.basketbuttonActiveOrNot();

            this.bmbSubmitButton.addEventListener('click', () => {

                if (localStorage.getItem('login')) {
                    if (document.getElementById('prd-btn').getAttribute('disabled')== null) {               
                        //CLICK Add Product insert in Basket:
                        page.addProductToBasket({product: pData, pictures: pPictures, quantity: sessionStorage.getItem('user-product-piece')});

                        sessionStorage.setItem('user-product-piece', 0);
                        sessionStorage.setItem('basketSwitch', 'basket');
                        page.pageLoad('kosar');
                    }
                }
            });

        } else {
            let loginButton = page.generateProduct('button', {'class': 'btn btn-primary submit-button' }, 'Belépés');
            let loginIcon = page.generateProduct('span', { 'class': 'button-ico bi-login' }, '');
            loginButton.appendChild(loginIcon);
            this.bmbSubmitButton.appendChild(loginButton);
            this.bmbSubmitButton.addEventListener('click', () => page.pageLoad('felhasznalo'));
        }

        //pictures list
        if (pPictures) {

            let autoRepeatText = '';
            for (let n=0; n < pPictures.length; n++) {
                if (n>2) { break; }
                autoRepeatText += "auto ";
            }

            this.prodPicturesContainer.style.gridTemplateColumns = autoRepeatText;
            pPictures.forEach(picture => {
                // black box
                let blackBox = page.generateProduct('div', {'class': 'blackBox' }, '');
                let blackBoxImg = page.generateProduct('img', {'src': '../backend/product-pictures/big_'+picture.serverfilename, 'class': 'blackbox-pic', 'alt': picture.text, 'title': picture.text }, '');
                let blackBoxText = page.generateProduct('div', {'class': 'blackbox-text theme-colorstyle01'}, picture.text);
                blackBox.appendChild(blackBoxText);
                blackBox.appendChild(blackBoxImg);
                this.productsSelectedDiv.appendChild(blackBox);
                //small pics
                let prodPicDiv = page.generateProduct('div', {'class': 'prod-pic-div'}, '');
                let prodImg = page.generateProduct('img', {'src': '../backend/product-pictures/small_'+picture.serverfilename, 'class': 'prod-pic', 'alt': picture.text, 'title': picture.text }, '');
                prodPicDiv.appendChild(prodImg);

                if (picture.text !='') {
                    let prodPicText = page.generateProduct('p', {'class': 'prod-pic-text theme-colorstyle01'}, picture.text);
                    prodPicDiv.appendChild(prodPicText);
                }

                prodPicDiv.addEventListener('click', () => {
                    blackBox.style.display = "flex";
                });
                
                blackBoxImg.addEventListener('click', () => {
                    blackBox.style.display = "none";
                });

                this.prodPicturesContainer.appendChild(prodPicDiv);
            });
            
        } else {
            // no have picture
            let prodPicDiv = page.generateProduct('div', {'class': 'prod-pic-div'}, '');
            let prodImg = page.generateProduct('img', {'src': '../backend/product-pictures/small_none.png', 'class': 'prod-pic', 'alt': 'Nincsen kép!', 'title': 'Nincsen kép!' }, '');
            let prodPicText = page.generateProduct('p', { 'class': 'prod-pic-text theme-colorstyle01'}, 'Nincsen kép!');

            prodPicDiv.appendChild(prodImg);
            prodPicDiv.appendChild(prodPicText);
            this.prodPicturesContainer.appendChild(prodPicDiv);
            this.prodPicturesContainer.style.gridTemplateColumns = "auto";
        }

        this.productsListDiv.style.display = 'none';
        this.productsSelectedDiv.style.display = 'block';
    }

    productsList = () => {

        this.insertProductTypes();
        
        this.productsGridboxDiv.innerHTML = '';
        this.productsGridboxDiv.style.display = 'none';

        this.productsSelectedDiv.style.display = 'none';
        this.productsListDiv.style.display = 'block';

        this.productsLoadingDiv.style.display = 'block';

        let sendData = { 
            filtertype: sessionStorage.getItem('filter-stlye'),
            filtername: sessionStorage.getItem('filter-name'),
            filterminprice: sessionStorage.getItem('filter-min'),
            filtermaxprice: sessionStorage.getItem('filter-max'),
            filtermarkdown: sessionStorage.getItem('filter-markdown')
        }
        
        api('post', '?product=productfilter', sendData)
        .then((productfilter) => {

            if (productfilter.status_code == 200) {

                //log('---'); log('stlye :'+sessionStorage.getItem('filter-stlye')); log('name :'+sessionStorage.getItem('filter-name')); log('min :'+sessionStorage.getItem('filter-min')); log('max :'+sessionStorage.getItem('filter-max')); log('markdown :'+sessionStorage.getItem('filter-markdown')); log('---');

                if (productfilter.response_data != null && productfilter.response_data != "none") {
                    // start list products
                    productfilter.response_data.forEach(product => {   
                        this.drawOneProduct(product);
                    });
                } else {
                    let textDiv = document.createElement('div');
                    textDiv.setAttribute('class', 'products-gridbox-message rounded mb-3 p-2');
                    textDiv.innerHTML = 'Nincsen találat.';
                    this.productsListDiv.appendChild(textDiv);
                }

                // Ha Betöltöttek a termékek:
                this.productsLoadingDiv.style.display = 'none';
                this.productsGridboxDiv.style.display = 'grid';

            } else {
                this.productsLoadingDiv.style.display = 'none';
                this.productsListDiv.innerHTML = '<div class="text-center text-danger p-3"><stong>Szerver hiba!</strong></div>';
            }
        });
    }

    drawOneProduct = (product) => {
        let sendProductId = { productid: product.id }
        api('post', '?product=picturelist', sendProductId)
        .then((pictsData) => {
                                            
            let pictFilename = (pictsData.status_code == 200 && pictsData.response_data != null) ? 'small_'+pictsData.response_data[0].serverfilename : 'small_none.png';

            let realPrice = (product.markdown !=0) ? page.countMarkdownPrice(product.price, product.markdown)  : product.price;

            let basketButton = (localStorage.getItem('login')) ? page.generateProduct('button', { 'class': 'product-basket-button btn btn-sm bg-warning mb-3 w-100'}, 'KOSÁRBA') : null;
            if (product.instock > 0) {
                if (basketButton) {
                    basketButton.addEventListener('click', () => {
                        // add to basket
                        page.addProductToBasket({product: product, pictures: pictsData.response_data, quantity: 1 });
                        sessionStorage.setItem('basketSwitch', 'basket');
                        page.pageLoad('kosar');
                    });
                }
            } else {
                (basketButton) ? basketButton.setAttribute('disabled', true) : false;
            }

            let span = page.generateProduct('span', { class: 'd-block'},'');
            let head = page.generateProduct('div', {'class': 'p-head'}, product.name);
            let img = page.generateProduct('img', {'class': 'product-pic', 'alt': product.name, 'title': product.name, 'src': '../backend/product-pictures/'+pictFilename }, '');
            let price = page.generateProduct('div', {'class': 'p-price'}, realPrice+'Ft');
            let markDown = page.generateProduct('div', {'class': 'product-div-markdown'}, product.markdown+'%');
            let productAll = page.generateProduct('div', {'class': 'product-div'},'');

            (product.markdown !=0) ? productAll.appendChild(markDown) : false;
            productAll.appendChild(span);
            productAll.appendChild(img);
            if (localStorage.getItem('login')) {
                span.appendChild(basketButton);
            }
            productAll.appendChild(price);
            productAll.appendChild(head);

            productAll.addEventListener('click', () => {
                if (product.instock > 0) {
                    sessionStorage.setItem('productSwitch', 'selected');
                    page.productSelectedData = {product: product, pictures: pictsData.response_data};
                    this.productsBasic();
                }
            });
            
            if (product.instock == 0) {
                img.addEventListener('click', () => {
                    sessionStorage.setItem('productSwitch', 'selected');
                    page.productSelectedData = {product: product, pictures: pictsData.response_data};
                    this.productsBasic();
                });
            }
                
            this.productsGridboxDiv.appendChild(productAll);

        });
    }

    filterAddEventListeners = () => {
                
        document.body.addEventListener('click', () =>{
            // nem marad az input módosítva ha kiklikkelnek belőle. 
            this.filterNameIn.value = sessionStorage.getItem('filter-name');
        });

        this.filterTypeIn.addEventListener('change', () => {

            let typeValue = document.getElementById('filter-typevalue');
            sessionStorage.setItem('filter-stlye', typeValue.value);          

            this.rangeMinMaxOptions();
            this.productsBasic();
        });

        this.filterNameButton.addEventListener('click', () => {
            sessionStorage.setItem('filter-name', this.filterNameIn.value.toLowerCase());
            this.productsBasic();
        });
        
        this.filterMinIn.addEventListener('change', () => {
            
            let newValue = (parseInt(this.filterMinIn.value) > parseInt(this.filterMaxIn.value)) ? this.filterMaxIn.value : this.filterMinIn.value;
            
            sessionStorage.setItem('filter-min', newValue);
            this.filterMinIn.value = sessionStorage.getItem('filter-min');
            
            this.productsBasic();
        });

        this.filterMaxIn.addEventListener('change', () => {
            
            let newValue = (parseInt(this.filterMaxIn.value) < parseInt(this.filterMinIn.value)) ? this.filterMinIn.value : this.filterMaxIn.value;
            
            sessionStorage.setItem('filter-max', newValue);
            this.filterMaxIn.value = sessionStorage.getItem('filter-max');
            this.productsBasic();
        });
        
        this.filterMarkdownIn.addEventListener('change', () => {
            let markdownValue = (this.filterMarkdownIn.checked) ? 1 : 0;
            sessionStorage.setItem('filter-markdown', markdownValue);
            this.productsBasic();
        });
    }

    selectProductAddEventListeners = () => {

        this.headTextButton.addEventListener('click', () => page.pageLoad('termekek'));

        this.bmbPlusValue.addEventListener('click', () => this.modifidProductPiece(+1));
        this.bmbMinusValue.addEventListener('click', () => this.modifidProductPiece(-1));

        this.bottomJump.addEventListener('click', () => {
            document.querySelector('.header').scrollIntoView({ behavior: 'smooth' });
        });
        
    }

    modifidProductPiece = (value) => {
        
        let newValue = (parseInt(sessionStorage.getItem('user-product-piece')))+(parseInt(value));
        if ((newValue >= 0) && (newValue <= sessionStorage.getItem('user-product-instock'))) {
            sessionStorage.setItem('user-product-piece', newValue);
            this.bmbInputValue.innerHTML = sessionStorage.getItem('user-product-piece');
            this.basketbuttonActiveOrNot();         
        }
    }

    basketbuttonActiveOrNot = () => {

        if (document.getElementById('prd-btn')) {
   
            if (sessionStorage.getItem('user-product-piece') == 0) {
                document.getElementById('prd-btn').setAttribute('disabled', true);
            } else {
                document.getElementById('prd-btn').removeAttribute('disabled');
            }
        }
    }

    productSelectedFavoriteLoad = (productid) => {

        this.favoriteButtonxy.classList.remove('favorite-on');
                
        let sendData = {
            token: localStorage.getItem('login'),
            productid: productid,
            function: "get"
        }
        
        api('post', '?user=favorites', sendData)
        .then((data) => {
            if (data.status_code == 200 || data.status_code == 201) {
                if (data.response_data) {
                    data.response_data.forEach(product => {
                        if (productid == product.id) {
                            this.favoriteButtonxy.classList.add('favorite-on');
                        }
                    });
                }
            }
        });
    }
}

class Favorites {
    userFavorites;
    constructor () {
        this.allFavoriteDiv = document.querySelector('#all-favorite-div');

        page.headFavoritButtonDraw();
        this.loadFavorites();
    }

    loadFavorites = () => {

        let sendData = {
            token: localStorage.getItem('login'),
            function: "get"
        }
        
        api('post', '?user=favorites', sendData)
        .then((data) => {
            if (data.status_code == 200 || data.status_code == 201) {

                if (data.response_data) {

                    this.userFavorites = data.response_data;
                    let productListFullrow = page.generateProduct('div', { class: 'row p-0 m-0 color2 text-black rounded mb-2 pt-1 pb-1' }, '');

                    this.userFavorites.forEach(product => {
                                               
                        let realPrice = (product.markdown == 0) ? product.price : page.countMarkdownPrice(product.price, product.markdown);
                        
                        let button1 = page.generateProduct('button', { productid: product.id, class: 'product-list-button delete-btn'}, '');
                        let div1 = page.generateProduct('div', { class: 'product-list-button-div'}, '');
                        let div2 = page.generateProduct('div', { class: 'product-list-row-rightside p-0 m-0'}, '');
                        div2.appendChild(div1);
                        div1.appendChild(button1);
                        let divI1 = page.generateProduct('div', { class: 'product-list-center-div col-12 col-sm-6 p-1 color-orange'}, realPrice);
                        let divI2 = page.generateProduct('div', { class: 'product-list-center-div col-12 col-sm-6 p-1 text-left'}, product.name);
                        let divG = page.generateProduct('div', { class: 'product-list-row-centerside row p-0 m-0'},'');
                        divG.appendChild(divI2);
                        divG.appendChild(divI1);
                        
                        let pictInsert = (product.serverfilename !== null) ? product.serverfilename : 'none.png';
                        
                        let img1 = page.generateProduct('img', { productid: product.id, class: 'basket-list-img', src: '../backend/product-pictures/small_'+pictInsert, alt: product.name, title: product.name },'');
                        let divR = page.generateProduct('div', { class: 'product-list-row-leftside p-1 mb-1'},'');
                        divR.appendChild(img1);
                        let prodList = page.generateProduct('div', { class: 'row product-list-fullrow theme-colorstyle01 col-12 p-0 m-0 mb-1'}, '');
                        prodList.appendChild(divR);
                        prodList.appendChild(divG);
                        prodList.appendChild(div2);
                           
                        productListFullrow.appendChild(prodList);

                    });

                    this.allFavoriteDiv.appendChild(productListFullrow);

                    // addeventListeners
                    document.querySelectorAll('img[productid]').forEach(element => {
                        let productId = element.getAttribute('productid');
                        element.addEventListener('click', () => {

                            let sendData = {
                                productid: productId
                            }

                            api('post', '?product=product', sendData)
                            .then(data => {
                                
                                sessionStorage.setItem('inc', 'termekek');
                                let inc = sessionStorage.getItem('inc');

                                page.pageLoad(inc);

                                sessionStorage.setItem('productSwitch', 'selected');
                                page.productSelectedData = {product: data.response_data.product, pictures: data.response_data.pictures };
                                
                            });
                        });
                    });

                    document.querySelectorAll('button[productid]').forEach(element => {
                        let productId = element.getAttribute('productid');
                        element.addEventListener('click', () => {

                            let sendData = {
                                token: localStorage.getItem('login'),
                                productid: productId,
                                function: "switch"
                            }
                            
                            api('post', '?user=favorites', sendData)
                            .then((data) => {
                                page.pageLoad(sessionStorage.getItem('inc'));
                            });
                            
                        });
                    });

                } else {
                    let noResult = page.generateProduct('span', { class: 'd-block text-center'},'Jelenleg nincsen kedvenced.');
                    this.allFavoriteDiv.appendChild(noResult);
                }
            }
        });
    }
}

class Basket {
    constructor () {  
        this.totalPriece = 0;

        this.allBasketDiv = document.querySelector('#all-basket-div');
        this.allAddressDiv = document.querySelector('#all-address-div');
        this.basketButtonBasket = document.querySelector('#basket-button-basket');
        this.basketButtonAddress = document.querySelector('#basket-button-address');
        
        this.addressUserName = document.querySelector('#ba-username');
        this.addressUserEmail = document.querySelector('#ba-useremail');
        this.addressUserInfo = document.querySelector('#ba-userinfo');

        this.basketSwitch = sessionStorage.getItem('basketSwitch');
        this.basketSwitch = (this.basketSwitch == null) ? 'basket' :  this.basketSwitch;
        
        this.basketBackButton = document.querySelector('#basket-back-button');
        this.basketBackButton.addEventListener('click', () => page.pageLoad('termekek'));
                
        this.baReferencenumber = document.querySelector('#ba-referencenumber');
        this.baCity = document.querySelector('#ba-city');
        this.baStreet = document.querySelector('#ba-street');
        this.baType = document.querySelector('#ba-type');
        this.baHousenumber = document.querySelector('#ba-housenumber');
        this.baMessage = document.querySelector('#ba-message');

        this.baCondition = document.querySelector('#ba-condition');
        this.orderButton = document.querySelector('#order-button');
        
        page.message.innerHTML = '';

        this.UserDatasDraw();
        this.basketButtonDraw();
        this.basketAddEventListeners();

        this.createBasketList();
    }

    UserDatasDraw = () => {
        let loadUserData = JSON.parse(localStorage.getItem('userdata'));

        if (!loadUserData) {
            page.reloadPage();
        }

        this.addressUserName.innerHTML = loadUserData.username;
        this.addressUserEmail.innerHTML = loadUserData.useremail;
        this.addressUserInfo.innerHTML = loadUserData.userinfo;
    }

    basketButtonDraw = () => {

        this.orderButtonCheck();
        if (sessionStorage.getItem('basketSwitch') == 'basket') {
            this.basketButtonAddress.classList.remove('bb-on');
            this.basketButtonBasket.classList.add('bb-on');
            this.allBasketDiv.style.display = 'block';
            this.allAddressDiv.style.display = 'none';
        } else {
            this.basketButtonBasket.classList.remove('bb-on');
            this.basketButtonAddress.classList.add('bb-on');
            this.allBasketDiv.style.display = 'none';
            this.allAddressDiv.style.display = 'block';
        }
        this.basketButtonBasket.scrollIntoView({ behavior: 'smooth' });
    }

    basketAddEventListeners = () => {

        this.baReferencenumber.addEventListener('keyup', () => this.baReferencenumber.value = this.baReferencenumber.value.slice(0, 4));

        this.baStreet.addEventListener('keyup', () => this.baStreet.value = this.baStreet.value);

        this.baCity.addEventListener('keyup', () => this.baCity.value = page.fistCharUpper(this.baCity.value));

        this.basketButtonBasket.addEventListener('click', () => { 
            sessionStorage.setItem('basketSwitch', 'basket');
            this.basketButtonDraw();
        });

        this.basketButtonAddress.addEventListener('click', () => { 
            sessionStorage.setItem('basketSwitch', 'address');
            this.basketButtonDraw();
        });

        // Megrendelés felvétele
        this.orderButton.addEventListener('click', () => {
            (this.orderButtonCheck()) ? this.orderManagement(): false;
        });

        this.baCondition.addEventListener('click', () => {
            if (this.baCondition.getAttribute('checked') == 'true') {
                this.baCondition.setAttribute('checked', false);
            } else {
                this.baCondition.setAttribute('checked', true);
            }
        });
    }

    orderManagement = () => {

        let userData = JSON.parse(localStorage.getItem('userdata'));

        let logMsg = '';
        try {
            page.lengthCheck('irányitószám', this.baReferencenumber.value, 4, 4, true);
        } catch (error) {
            logMsg += error;
        }
        try {
            page.lengthCheck('város', this.baCity.value, 2, 25);
        } catch (error) {
            logMsg += error;
        }
        try {
            page.lengthCheck('megnevezés', this.baStreet.value, 2, 30);
        } catch (error) {
            logMsg += error;
        }
        try {
            page.lengthCheck('házszám', this.baHousenumber.value, 1, 999);
        } catch (error) {
            logMsg += error;
        }
        try {
            if (this.baCondition.getAttribute('checked') != 'true') {
                throw '<li>A felhasználási és rendelési feltételeket el kell fogani!</li>';
            }
        } catch (error) {
            logMsg += error;
        }
        
        // Order recording
        if (logMsg == '') {
            this.baMessage.innerHTML = '';

            let code = Math.floor(Math.random()*899999+100000);
            
            let sendData = {
                token: localStorage.getItem('login'),
                userid: userData.userid,
                productlist: localStorage.getItem('basketContent'),
                totalprice: this.totalPriece,
                postalcode: this.baReferencenumber.value,
                city: this.baCity.value,
                designation: this.baStreet.value,
                designationtype: this.baType.value,
                designationnumber: this.baHousenumber.value,
                code: code
            }

            $.ajax({
                method: 'POST',
                url: 'confirmation.php',
                data: {code: code, userid: userData.userid, useremail: userData.useremail},
                success: function(responseData) {
                    orderRecording(responseData, sendData);
                }
            });

        } else {
            this.baMessage.innerHTML = logMsg;
        }
    }

    orderButtonCheck = () => {

        let basketContent = JSON.parse(localStorage.getItem('basketContent'));
        let have = false;

        if (basketContent !== null) {
            if (basketContent.length !== 0) {
                have = true;
                this.orderButton.removeAttribute('disabled');
                return true;
            }
        }

        (!have) ? this.orderButton.setAttribute('disabled', true) : false;
        return false;
    }

    createBasketList = () => {
        
        var basketContent = '';

        if (localStorage.getItem('basketContent')) {
            basketContent = JSON.parse(localStorage.getItem('basketContent'));
        }

        if (basketContent !== null && basketContent.length !== 0) {

            var productListFullrow = page.generateProduct('div', { class: 'row m-0 p-0' }, '');

            let upper = 0;
            let total = 0;

            basketContent.forEach(product => {
                
                let realPrice = (product.product.markdown == 0) ? product.product.price : page.countMarkdownPrice(product.product.price, product.product.markdown);
                
                total = total + (realPrice * product.quantity);

                let button1 = page.generateProduct('button', { arrayid: upper, class: 'product-list-button delete-btn'}, '');
                let div1 = page.generateProduct('div', { class: 'product-list-button-div'}, '');
                let div2 = page.generateProduct('div', { class: 'product-list-row-rightside p-0 m-0'}, '');
                div2.appendChild(div1);
                div1.appendChild(button1);
                let divI1 = page.generateProduct('div', { class: 'product-list-center-div col-12 col-sm-4 p-1 color-orange'}, realPrice+' Ft');
                let divI2 = page.generateProduct('div', { class: 'product-list-center-div col-12 col-sm-4 p-1 text-left'}, product.product.name);
                
                let  pmBox = page.generateProduct('div', {arrayid: upper, 'class': 'list-basket-much-button mt-1'}, '');
                let  pmBoxIn1 = page.generateProduct('div', {id: 'list-bmb-minus-value', class:'list-bmb bmb-minus'}, '');
                let  pmBoxIn2 = page.generateProduct('div', {id: 'list-bmb-input-value', class:'list-bmb-how'}, product.quantity+'');
                let  pmBoxIn3 = page.generateProduct('div', {id: 'list-bmb-plus-value', class:'list-bmb list-bmb-plus'}, '');

                pmBox.appendChild(pmBoxIn1);
                pmBox.appendChild(pmBoxIn2);
                pmBox.appendChild(pmBoxIn3);

                let divI3 = page.generateProduct('div', { class: 'product-list-center-div col-12 col-sm-4 text-left'}, '');
                divI3.appendChild(pmBox);

                let divG = page.generateProduct('div', { class: 'product-list-row-centerside row p-0 m-0'},'');
                divG.appendChild(divI2);
                divG.appendChild(divI3);
                divG.appendChild(divI1);
                
                let pictInsert = (product.pictures !== null) ? product.pictures[0].serverfilename : 'none.png';

                let img1 = page.generateProduct('img', { productid: product.product.id, class: 'basket-list-img', src: '../backend/product-pictures/small_'+pictInsert, alt: product.product.name, title: product.product.name },'');
                let divR = page.generateProduct('div', { class: 'product-list-row-leftside p-1 mb-1'},'');
                
                divR.appendChild(img1);
                let prodList = page.generateProduct('div', { class: 'row product-list-fullrow theme-colorstyle01 col-12 p-0 m-0 mb-1'}, '');
                prodList.appendChild(divR);
                prodList.appendChild(divG);
                prodList.appendChild(div2);
                
                productListFullrow.appendChild(prodList);

                upper++;
            });

            this.totalPriece = total;
            let totalDiv = page.generateProduct('div', {'class': 'product-list-total theme-colorstyle01 col-12 p-2 m-0 rounded-bottom'}, '');
            let totalText = page.generateProduct('span', {'class': 'total-text'}, 'Összesen:');
            let totalCoast = page.generateProduct('span', {'class': 'total-coast'}, total + ' Ft');
            
            totalDiv.appendChild(totalText);
            totalDiv.appendChild(totalCoast);
            productListFullrow.appendChild(totalDiv);
            this.allBasketDiv.appendChild(productListFullrow);

            ////// addeventListeners

            // back SOLO product
            document.querySelectorAll('img[productid]').forEach(element => {
                let productId = element.getAttribute('productid');
                element.addEventListener('click', () => {
                    
                    let sendData = {
                        productid: productId
                    }
                    
                    api('post', '?product=product', sendData)
                    .then(data => {
                        
                        sessionStorage.setItem('inc', 'termekek');
                        let inc = sessionStorage.getItem('inc');

                        page.pageLoad(inc);
                        
                        sessionStorage.setItem('productSwitch', 'selected');
                        page.productSelectedData = {product: data.response_data.product, pictures: data.response_data.pictures };
                        
                    });
                });
            });

            // del in basket
            document.querySelectorAll('button[arrayid]').forEach(element => {
                
                element.addEventListener('click', () => {
                    
                    let clickRowId = element.getAttribute('arrayid');
                    this.delBasketRow(clickRowId);
                    page.pageLoad('kosar');
                });         
            });

            // MUCH + -
            document.querySelectorAll('.list-basket-much-button').forEach(listRow => {

                let arrayId = listRow.getAttribute('arrayid');
                let value = parseInt(listRow.querySelector('#list-bmb-input-value').innerHTML);

                listRow.querySelector('#list-bmb-minus-value').addEventListener('click', () => {
                    
                    if (value > 1) {
                        basketContent[arrayId].quantity = basketContent[arrayId].quantity-1;
                        localStorage.setItem('basketContent', JSON.stringify(basketContent));
                    } else {
                        this.delBasketRow(arrayId);
                    }
                    page.pageLoad('kosar');        
                });

                listRow.querySelector('#list-bmb-plus-value').addEventListener('click', () => {
                    
                    if (value < basketContent[arrayId].product.instock) {
                        basketContent[arrayId].quantity = basketContent[arrayId].quantity+1;
                        localStorage.setItem('basketContent', JSON.stringify(basketContent));
                    }
                    page.pageLoad('kosar');
                });
            });

        } else {
            var productListFullrow = page.generateProduct('div', { class: 'd-flex color2 justify-content-center p-2 rounded mb-2' }, 'A kosár üres.');
            this.allBasketDiv.appendChild(productListFullrow);
        }
    }

    delBasketRow = (row) => {

        let basketContent = JSON.parse(localStorage.getItem('basketContent'));
        basketContent.splice(row, 1);
        localStorage.setItem('basketContent', JSON.stringify(basketContent));
    }
}

class Contact {
    constructor () {
        this.emailDiv = document.getElementById('emailDiv');
        this.emailDataDiv = document.getElementById('emailDataDiv');

        this.emailMsg = document.querySelector('#email-message');
        this.emailSubmit = document.querySelector('#emailSubmit');

        this.senderAddress = document.querySelector('#senderAddress');
        this.senderName = document.querySelector('#senderName');
        this.emailsubject = document.querySelector('#emailsubject');
        this.msgBody = document.querySelector('#msgbody');

        this.emailDataDiv.style.display = 'block';

        this.emailSubmit.addEventListener('click', () => {

            let emailMsg = '';

            try {
                if (!(page.emailCheck(this.senderAddress.value))) {
                    throw '<li>Az email nem email formátum!</li>';
                }
            }
            catch (error) { emailMsg += error; }

            try { 
                page.lengthCheck('feladó név', this.senderName.value, 4, 30);
            }
            catch (error) { emailMsg += error; }

            try { 
                page.lengthCheck('tárgy', this.emailsubject.value, 4, 60);
            }
            catch (error) { emailMsg += error; }

            try {
                page.lengthCheck('üzenet', this.msgBody.value, 6, 255);
            }
            catch (error) { emailMsg += error; }

            if (emailMsg=='') {

                let senderAddress = this.senderAddress.value;
                let senderName = this.senderName.value;
                let emailsubject = this.emailsubject.value;
                let msgBody = this.msgBody.value;

                $.ajax({
                    method: 'POST',
                    url: 'email.php',
                    data: {senderAddress: senderAddress, senderName: senderName, emailsubject: emailsubject, msgBody: msgBody },
                    success: function(data) {
                        $('#email-message').html(data);
                       
                        senderAddress = '';
                        senderName = '';
                        emailsubject = '';
                        msgBody = '';
                    }
                });

                this.emailDataDiv.style.display = 'none';
                this.emailDiv.scrollIntoView();

                this.senderAddress.value = '';
                this.senderName.value = '';
                this.emailsubject.value = '';
                this.msgBody.value = '';

            } else {
                this.emailMsg.innerHTML = emailMsg;
                this.emailDiv.scrollIntoView();
            }

        });
    }
}

async function api(method, query, data) {
    let options = {
        method: method,
        mode: 'cors',
        cache: 'no-cache',
        credentials: 'same-origin',
        redirect: 'follow',
        referrerPolicy: 'no-referrer',
        headers: { 'Content-Type': 'application/json' }
    }

    if (method =='post') {
        options['body'] = JSON.stringify(data);
    }

    let response = await fetch(fileAccess + query, options)
    return response.json();
}

orderRecording = (responseReply, sendData) => {
    if (responseReply) {
        api('post', '?order=add', sendData)
        .then ((data) => {
            document.querySelector('#ba-message').innerHTML = '';
            if (data.status_code == 200 || data.status_code == 201) {
                localStorage.setItem('basketContent', JSON.stringify(null));
                sessionStorage.setItem('inc', 'bemutatkozas');
                page.pageLoad('bemutatkozas');
                let messageDiv = page.generateProduct('div', {class: 'message bg-success'}, '');
                let messageButton = page.generateProduct('button', {id: 'orderOkButton', class:'btn btn-sm btn-warning ms-2'}, 'Rendben');
                let messageText = page.generateProduct('span', {class: 'text-center'}, data.response_data);
                messageDiv.appendChild(messageText);
                messageDiv.appendChild(messageButton);
                document.querySelector('#message').appendChild(messageDiv);
                messageButton.addEventListener('click', () => document.querySelector('#message').innerHTML = '' );
            }
        });
    } else {
        document.querySelector('#ba-message').innerHTML = 'Sikertelen megrendelés!';
    }
}

var page = new Page();