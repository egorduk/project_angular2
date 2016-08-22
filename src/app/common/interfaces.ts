export interface IPicture {
    id: number;
    name: string;
    filename: string;
    comments: IComment[];
}

export interface IUser {
    id: number;
    email: string;
    login: string;
    avatar: string;
    password: string;
    pictures: IPicture[];
    is_liked: boolean;
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