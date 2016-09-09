export class IPicture {
    id: number;
    name: string;

    constructor(id: number, name: string) {
        this.id = id;
        this.name = name;
    }
    //filename: string;
    //comments: IComment[];
}

export interface IUser {
    id: number;
    email: string;
    login: string;
    avatar: string;
    password: string;
    info: string;
    page_photo: string;
    pictures: IPicture[];
}

export interface IComment {
    id: number;
    comment: string;
    user_id: number;
    picture_id: number;
}

export interface IGallery {
    id: number;
    name: string;
}