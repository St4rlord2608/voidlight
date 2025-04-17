import { clsx, type ClassValue } from 'clsx';
import { twMerge } from 'tailwind-merge';

const tempUserIdKey = 'tempId'
const tempNameKey = 'tempName'

export function cn(...inputs: ClassValue[]) {
    return twMerge(clsx(inputs));
}

export function initializeTempUserId(){
    let tempUserId = "";
    try{
        const storedId = localStorage.getItem(tempUserIdKey);
        if(storedId){
            tempUserId = storedId;
        }else{
            tempUserId = "guest-" + createRandomId();
            localStorage.setItem(tempUserIdKey, tempUserId)
        }
    }catch(error){
        console.error('Error accessing localStorage for User ID: ', error)
    }
    return tempUserId;
}

export function initializeTempName(){
    let tempName = "";
    try{
        const storedName = localStorage.getItem(tempNameKey);
        if(storedName){
            tempName = storedName;
        }else{
            tempName = "guest-name-" + createRandomId();
            localStorage.setItem(tempNameKey, tempName)
        }
    }catch(error){
        console.error('Error accessing localStorage for User Name: ', error)
    }
    return tempName;
}

export function setTempName(tempName : string){
    if(tempName == '')return;
    try{
        localStorage.setItem(tempNameKey, tempName)
    }catch(error){
        console.error('Error setting localStorage for User Name: ', error)
    }
}

export function createRandomId(length = 10) : string{
    const chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    let id = "";
    const maxIndex = chars.length - 1;

    for (let i = 0; i < length; i++){
        const randomIndex = Math.floor(Math.random() * (maxIndex + 1));
        id += chars[randomIndex];
    }
    return id;
}
