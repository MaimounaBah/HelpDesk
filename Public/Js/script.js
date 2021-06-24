
// NOTIFICATIONS
document.querySelector(".notification-icon").addEventListener('click', e => {

    let notification_container = document.querySelector(".notification-container")

    console.log(notification_container.className)
    if (notification_container.className == "notification-container"){
        notification_container.classList.add('active')
    } else {
        notification_container.classList.remove('active')
    }
    
})


// PROFIL
document.querySelector(".profile").addEventListener('click', e => {

    let profil_container = document.querySelector(".profile-container")

    console.log(profil_container.className)
    if (profil_container.className == "profile-container"){
        profil_container.classList.add('active')
    } else {
        profil_container.classList.remove('active')
    }
    
})

// MENU
document.getElementById("toggle").addEventListener('click', e => {

    let menu = document.querySelector(".menu")

    console.log(menu.className)
    if (menu.className == "menu"){
        menu.classList.add('active')
    } else {
        menu.classList.remove('active')
    }
    
})

document.querySelector("li.dropdown").addEventListener('click', e => {

    next = e.target.nextElementSibling
    console.log(e)
    // next.classList.add('extend')
    if (next.classList.contains('extend'))
    {
        next.classList.remove('extend')
    } else{
        next.classList.add('extend')
    }

})