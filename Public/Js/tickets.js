// MENU DETAIL TICKET (SUPPRIMER/MODIFIER/REDIRIGER/CLOTURER)

document.getElementById("show-menu").addEventListener('click', e => {

    let menu = document.querySelector(".ellipsis-menu")

    if (menu.className == "ellipsis-menu"){
        menu.classList.add('active-toogle')
    } else {
        menu.classList.remove('active-toogle')
    }
    
})