import { Injectable } from '@angular/core';
import { Http, Response, HTTP_PROVIDERS } from '@angular/http';
//Grab everything with import 'rxjs/Rx';
import { Observable } from 'rxjs/Observable';
import {Observer} from 'rxjs/Observer';
import 'rxjs/add/operator/map'; 
import 'rxjs/add/operator/catch';

import { ICustomer, IOrder, IState, IPicture, IUser } from '../interfaces';

@Injectable()
export class DataService {
  
    _baseUrl: string = '';
    customers: ICustomer[];
    orders: IOrder[];
    states: IState[];
    pictures: IPicture[];
    user: IUser;
    _serverUrl: string = '';

    constructor(private http: Http) {
       this._serverUrl = 'http://localhost:80/project_angular2/src/app/server/server.php';
    }

    loginUser(user: IUser) : Observable<boolean> {
        /*let headers = new Headers({
            'Content-Type': 'application/json'});

        return this.http
            .post(this.serverUrl + '?action=login_user', JSON.stringify(user), {headers: headers})
            .toPromise()
            .then(res => res.json().data)
            .catch(this.handleError);*/

        return this.http.get(this._serverUrl + '?action=get_user&login=' + user.login + '&password=' + user.password)
            .map((res: Response) => {
                //this.pictures = res.json();
                console.log(res);
                //return this.user;
                return true;
            })
            .catch(this.handleError);
    }

    getPictures() : Observable<IPicture[]> {
        if (!this.pictures) {
            //return this.http.get(this._baseUrl + 'app/server/server.php')
            return this.http.get(this._serverUrl + '?action=get_pictures')
            //return this.http.get(this._baseUrl + 'pictures.json')
                .map((res: Response) => {
                    this.pictures = res.json();
                    return this.pictures;
                })
                .catch(this.handleError);
        } else {
            //return cached data
            return this.createObservable(this.pictures);
        }
    }
    
    getCustomers() : Observable<ICustomer[]> {
        if (!this.customers) {
            return this.http.get(this._baseUrl + 'customers.json')
                        .map((res: Response) => {
                            this.customers = res.json();
                            return this.customers;
                        })
                        .catch(this.handleError);
        }
        else {
            //return cached data
            return this.createObservable(this.customers);
        }
    }
    
    getCustomer(id: number) : Observable<ICustomer> {
        if (this.customers) {
            //filter using cached data
            return this.findCustomerObservable(id);
        } else {
            //Query the existing customers to find the target customer
            return Observable.create((observer: Observer<ICustomer>) => {
                    this.getCustomers().subscribe((customers: ICustomer[]) => {
                        this.customers = customers;                
                        const cust = this.filterCustomers(id);
                        observer.next(cust);
                        observer.complete();
                })
            })
            .catch(this.handleError);
        }
    }

    getOrders(id: number) : Observable<IOrder[]> {
      return this.http.get(this._baseUrl + 'orders.json')
                .map((res: Response) => {
                    this.orders = res.json();
                    return this.orders.filter((order: IOrder) => order.customerId === id);
                })
                .catch(this.handleError);               
    }
    
    updateCustomer(customer: ICustomer) : Observable<boolean> {
        return Observable.create((observer: Observer<boolean>) => {
            this.customers.forEach((cust: ICustomer, index: number) => {
               if (cust.id === customer.id) {
                   const state = this.filterStates(customer.state.abbreviation);
                   customer.state.abbreviation = state.abbreviation;
                   customer.state.name = state.name;
                   this.customers[index] = customer;
               } 
            });
            observer.next(true);
            observer.complete();
        });
    }
    
    getStates(): Observable<IState[]> {
        if (this.states) {
            return Observable.create((observer: Observer<IState[]>) => {
                observer.next(this.states);
                observer.complete();
            });
        } else {
            return this.http.get(this._baseUrl + 'states.json').map((response: Response) => {
                this.states = response.json();
                return this.states;
            })
            .catch(this.handleError);
        }
    }
    
    private findCustomerObservable(id: number) : Observable<ICustomer> {        
        return this.createObservable(this.filterCustomers(id));
    }
    
    private filterCustomers(id: number) : ICustomer {
        const custs = this.customers.filter((cust) => cust.id === id);
        return (custs.length) ? custs[0] : null;
    }
    
    private createObservable(data: any) : Observable<any> {
        return Observable.create((observer: Observer<any>) => {
            observer.next(data);
            observer.complete();
        });
    }
    
    private filterStates(stateAbbreviation: string) {
        const filteredStates = this.states.filter((state) => state.abbreviation === stateAbbreviation);
        return (filteredStates.length) ? filteredStates[0] : null;
    }
    
    private handleError(error: any) {
        console.error(error);
        return Observable.throw(error.json().error || 'Server error');
    }

}
