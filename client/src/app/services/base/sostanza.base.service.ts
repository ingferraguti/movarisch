/**
 *
 *
  _____                      _              _ _ _     _   _     _        __ _ _
 |  __ \                    | |            | (_) |   | | | |   (_)      / _(_) |
 | |  | | ___    _ __   ___ | |_    ___  __| |_| |_  | |_| |__  _ ___  | |_ _| | ___
 | |  | |/ _ \  | '_ \ / _ \| __|  / _ \/ _` | | __| | __| '_ \| / __| |  _| | |/ _ \
 | |__| | (_) | | | | | (_) | |_  |  __/ (_| | | |_  | |_| | | | \__ \ | | | | |  __/
 |_____/ \___/  |_| |_|\___/ \__|  \___|\__,_|_|\__|  \__|_| |_|_|___/ |_| |_|_|\___|

 * DO NOT EDIT THIS FILE!!
 *
 *  FOR CUSTOMIZE sostanzaBaseService PLEASE EDIT ../sostanza.service.ts
 *
 *  -- THIS FILE WILL BE OVERWRITTEN ON THE NEXT SKAFFOLDER'S CODE GENERATION --
 *
 */
 // DEPENDENCIES
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { map } from 'rxjs/operators';
import { HttpClient } from '@angular/common/http';

// CONFIG
import { environment } from '../../../environments/environment';

// MODEL
import { Sostanza } from '../../domain/movarisch_db/sostanza';

/**
 * THIS SERVICE MAKE HTTP REQUEST TO SERVER, FOR CUSTOMIZE IT EDIT ../Sostanza.service.ts
 */

/*
 * SCHEMA DB Sostanza
 *
	{
		Identificativo: {
			type: 'String'
		},
		Nome: {
			type: 'String',
			required : true
		},
		Score: {
			type: 'Decimal'
		},
		VLEP: {
			type: 'Boolean',
			required : true
		},
		//RELATIONS
		//EXTERNAL RELATIONS
		FrasiH: [{
			type: Schema.ObjectId,
			ref : "Sostanza"
		}],
		Sostanza: [{
			type: Schema.ObjectId,
			ref : "Miscelanonpericolosa"
		}],
		Sostanza: [{
			type: Schema.ObjectId,
			ref : "Processo"
		}],
		User: {
			type: Schema.ObjectId,
			ref : "Sostanza"
		},
	}
 *
 */
@Injectable()
export class SostanzaBaseService {

    contextUrl: string = environment.endpoint + '/sostanza';
    constructor(
        protected http: HttpClient
        ) { }

    // CRUD METHODS

    /**
    * SostanzaService.create
    *   @description CRUD ACTION create
    *
    */
    create(item: Sostanza): Observable<Sostanza> {
        return this.http
            .post<Sostanza>(this.contextUrl, item)
            .pipe(map(data => data));
    }

    /**
    * SostanzaService.delete
    *   @description CRUD ACTION delete
    *   @param ObjectId id Id
    *
    */
    remove(id: string): Observable<void> {
        return this.http
            .delete<void>(this.contextUrl + '/' + id)
            .pipe(map(data => data));
    }

    /**
    * SostanzaService.findByFrasiH
    *   @description CRUD ACTION findByFrasiH
    *   @param Objectid key Id della risorsa FrasiH da cercare
    *
    */
    findByFrasiH(id: string): Observable<Sostanza[]> {
        return this.http
            .get<Sostanza[]>(this.contextUrl + '/findByFrasiH/' + id)
            .pipe(
                map(response => response)
            );
    }

    /**
    * SostanzaService.findByUser
    *   @description CRUD ACTION findByUser
    *   @param Objectid key Id of the resource User to search
    *
    */
    findByUser(id: string): Observable<Sostanza[]> {
        return this.http
            .get<Sostanza[]>(this.contextUrl + '/findByUser/' + id)
            .pipe(
                map(response => response)
            );
    }

    /**
    * SostanzaService.findByVLEP
    *   @description CRUD ACTION findByVLEP
    *   @param Objectid key Id of the resource VLEP to search
    *
    */
    findByVLEP(id: string): Observable<Sostanza[]> {
        return this.http
            .get<Sostanza[]>(this.contextUrl + '/findByVLEP/' + id)
            .pipe(
                map(response => response)
            );
    }

    /**
    * SostanzaService.get
    *   @description CRUD ACTION get
    *   @param ObjectId id Id 
    *
    */
    get(id: string): Observable<Sostanza> {
        return this.http
            .get<Sostanza>(this.contextUrl + '/' + id)
            .pipe(map(data => data));
    }

    /**
    * SostanzaService.getFrasiH
    *   @description CRUD ACTION getFrasiH
    *   @param Objectid id ID of Sostanza from FrasiH
    *
    */
    getFrasiH(id: string): Observable<any[]> {
        return this.http
            .get<any[]>(this.contextUrl + '/' + id + 'getFrasiH')
            .pipe(
                map(response => response)
            );
     }

    /**
    * SostanzaService.list
    *   @description CRUD ACTION list
    *
    */
    list(): Observable<Sostanza[]> {
        return this.http
            .get<Sostanza[]>(this.contextUrl)
            .pipe(map(data => data));
    }

    /**
    * SostanzaService.update
    *   @description CRUD ACTION update
    *   @param ObjectId id Id
    *
    */
    update(item: Sostanza): Observable<Sostanza> {
        return this.http
            .post<Sostanza>(this.contextUrl + '/' + item._id, item)
            .pipe(map(data => data));
    }


    // Custom APIs

}
