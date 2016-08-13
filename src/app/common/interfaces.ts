export interface IPicture {
    id: number;
    name: string;
    filename: string;
}

export interface IUser {
    id: number;
    email: string;
    login: string;
    avatar: string;
    password: string;
    pictures: IPicture[];
}