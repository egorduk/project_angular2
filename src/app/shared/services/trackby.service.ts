import { Injectable } from '@angular/core';

import { ICustomer, IOrder, IPicture } from '../interfaces';

@Injectable()
export class TrackByService {

    customer(index:number, customer: ICustomer) {
        return customer.id;
    }

    picture(index:number, picture: IPicture) {
        return picture.id;
    }
}