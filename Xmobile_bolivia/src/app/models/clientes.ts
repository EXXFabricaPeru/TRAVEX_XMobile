import { Databaseconf } from "./databaseconf";
import { ConfigService } from "./config.service";
import {Camposusuario} from "../services/camposusuario.service"


export class Clientes extends Databaseconf {
    public configService: ConfigService;
    public Camposusuario: Camposusuario;

    public async updateAddAnticipos(total: any, CardCode: any) {
        let sql = `UPDATE clientes SET anticipos = (CAST(anticipos AS FLOAT) + ${total}) WHERE CardCode = '${CardCode}';`;
        return await this.executeSQL(sql);
    }

    public async updateRmAnticipos(total: any, CardCode: any) {
        let sql = `UPDATE clientes SET anticipos = (CAST(anticipos AS FLOAT) - ${total}) WHERE CardCode = '${CardCode}';`;
        return await this.executeSQL(sql);
    }


    public async updatebalancemas(total: any, CardCode: any) {
        let sql = `UPDATE clientes SET CurrentAccountBalance = (CurrentAccountBalance + ${total}) WHERE CardCode = '${CardCode}';`;
        console.log("sql updatebalancemas ", sql);
        return await this.executeSQL(sql);
    }

    public async updatebalancemenos(total: any, CardCode: any) {
        let sql = `UPDATE clientes SET CurrentAccountBalance = (CurrentAccountBalance - ${total}) WHERE CardCode = '${CardCode}';`;
        console.log("sql updatebalancemenos ", sql);
        return await this.executeSQL(sql);
    }

    public async updatebalancemenossap(valor: any, CardCode: any) {
        let sql = `UPDATE clientes SET CurrentAccountBalance = ${valor} WHERE CardCode = '${CardCode}';`;
        console.log("sql updatebalancemenos ", sql);
        return await this.executeSQL(sql);
    }


    public async findClienteUbicaciones() {
        let sql = `SELECT * FROM clientes WHERE Latitude != '0' AND Longitude != '0';`;
        return await this.queryAll(sql);
    }

    public async find(code: any) {
        let sql = `SELECT * FROM clientes WHERE CardCode = '${code}'`;
        console.log(sql);
        return await this.queryAll(sql);
    }

    public async findcount(code: any) {
        let sql = `SELECT count(*) as cantidad FROM clientes WHERE CardCode = '${code}'`;
        console.log(sql);
        return await this.queryAll(sql);
    }


    public async insertAll(objeto: any, idx: number, contador = 0) {
        let obj = JSON.parse(objeto.data)?JSON.parse(objeto.data):[];        
        if (contador == 0) {
            let sql = 'DELETE FROM clientes;';
            await this.exe(sql);
/*
            if (idx > 0) {
                let sql = 'DELETE FROM clientes';
                await this.exe(sql);
                obj = JSON.parse(objeto.data);
            } else {
                obj = objeto[0];
            }
        */        }
        if (obj && obj.respuesta && !obj.respuesta.length){ 
            console.log("ingrso aqui....!",obj)
            return Promise.resolve(true); 
        }
        return new Promise(async (resolve, reject) => {
            let sql = 'INSERT INTO clientes VALUES ';
            let campos = new Camposusuario();
            let session = await campos.consultasesion();

            for (let d of obj.respuesta) {                
                let sql2 = await campos.camposusuariosinc(d,3,session);
                
                if (d.GroupName == "La Paz") {
                    console.log("d ======== LA paZ", d);
                }
                let diavisitas = '';
                if (d.lunes == 'Y')
                    diavisitas += 'Lunes,';
                if (d.martes == 'Y')
                    diavisitas += 'Martes,';
                if (d.miercoles == 'Y')
                    diavisitas += 'Miercoles,';
                if (d.jueves == 'Y')
                    diavisitas += 'Jueves,';
                if (d.viernes == 'Y')
                    diavisitas += 'Viernes,';
                if (d.sabado == 'Y')
                    diavisitas += 'Sábado,';
                if (d.domingo == 'Y')
                    diavisitas += 'Domingo,';
                if (d.personacontactocelular == '')
                    d.personacontactocelular = 0;

                console.log("REMPLAZA",d.Address.replace(/['"]+/g, ''));
                    
                let CardName = d.CardName;
                let razonsocial = d.razonsocial;
                let Address = d.Address.replace(/['"]+/g, '');

                //let sql = 'INSERT INTO clientes VALUES ';
                 sql += ` (NULL ,'${d.img}','${idx}','${d.CardCode}','${CardName}','${d.CardType}','${Address}',
                '${d.CreditLimit}','${d.MaxCommitment}','${d.DiscountPercent}','${d.PriceListNum}',
                '${d.SalesPersonCode}','${d.Currency}','${d.County}','${d.Country}','${d.CurrentAccountBalance}',
                '${d.NoDiscounts}','${d.PriceMode}','${d.FederalTaxId}','${d.PhoneNumber}','${d.ContactPerson}',
                '${d.PayTermsGrpCode}','${d.Latitude}','${d.Longitude}','${d.GroupCode}','${d.User}',
                '${d.Status}','${d.DateUpdate}',0,'',10,'${d.celular}','${d.personacontactocelular}',
                '${d.correoelectronico}','${d.Territory}','${d.Description}','${diavisitas}', '${d.comentario}',
                '','','','','${razonsocial}','${d.tipoEmpresa}','${d.cliente_std1}','${d.cliente_std2}','${d.cliente_std3}',
                '${d.cliente_std4}','${d.cliente_std5}','${d.cliente_std6}','${d.cliente_std7}','${d.cliente_std8}',
                '${d.cliente_std9}','${d.cliente_std10}','${d.anticipos}','${d.cndpago}','${d.cndpagoname}'
                ,'${d.grupoSIN}','${d.iva}','${d.DescuentoG}','${d.DescuentoC}','${d.DescuentoCC}','${d.DescuentoA}', '${d.GroupName}',
                '${d.codeCanal}','${d.codeSubCanal}','${d.codeTipoTienda}','${d.cadena}','${d.cadenaconsolidador}','${d.cadenatxt}','${d.Mobilecod}', 
                '${d.cadenaCcus}', '${d.Fex_tipodocumento}','${d.Fex_complemento}','${d.Fex_codigoexcepcion}','${d.activo}','${d.Territory}','',
                '${d.U_EXX_TIPOPERS}','${d.U_EXX_TIPODOCU}','${d.U_EXX_APELLPAT}','${d.U_EXX_APELLMAT}','${d.U_EXX_PRIMERNO}','${d.U_EXX_SEGUNDNO}'
                `+sql2+`),`; 
            
                // sql += ` (NULL ,'${d.img}','${idx}','${d.CardCode}','${CardName}','${d.CardType}','${Address}',
                // '${d.CreditLimit}','${d.MaxCommitment}','${d.DiscountPercent}','${d.PriceListNum}',
                // '${d.SalesPersonCode}','${d.Currency}','${d.County}','${d.Country}','${d.CurrentAccountBalance}',
                // '${d.NoDiscounts}','${d.PriceMode}','${d.FederalTaxId}','${d.PhoneNumber}','${d.ContactPerson}',
                // '${d.PayTermsGrpCode}','${d.Latitude}','${d.Longitude}','${d.GroupCode}','${d.User}',
                // '${d.Status}','${d.DateUpdate}',0,'',10,'${d.celular}','${d.personacontactocelular}',
                // '${d.correoelectronico}','${d.Territory}','${d.Description}','${diavisitas}', '${d.comentario}',
                // '','','','','${d.tipoEmpresa}','${d.cliente_std1}','${d.cliente_std2}','${d.cliente_std3}',
                // '${d.cliente_std4}','${d.cliente_std5}','${d.cliente_std6}','${d.cliente_std7}','${d.cliente_std8}',
                // '${d.cliente_std9}','${d.cliente_std10}','${d.anticipos}','${d.cndpago}','${d.cndpagoname}'
                // ,'${d.grupoSIN}','${d.iva}','${d.DescuentoG}','${d.DescuentoC}','${d.DescuentoCC}','${d.DescuentoA}', '${d.GroupName}',
                // '${d.codeCanal}','${d.codeSubCanal}','${d.codeTipoTienda}','${d.cadena}','${d.cadenaconsolidador}','${d.cadenatxt}','${d.Mobilecod}', 
                // '${d.cadenaCcus}', '${d.Fex_tipodocumento}','${d.Fex_complemento}','${d.Fex_codigoexcepcion}','${d.activo}','${d.Territory}',''`+sql2+`),`; 
                //console.log(sql);
                //let sqlx = sql.slice(0, -1);
                //let respuesta = await this.insertrdata(sqlx);
            }
            
            console.log(sql);
            let sqlx = sql.slice(0, -1);
            this.executeSQL(sqlx).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                console.log("error al insertar clientes", e)
                reject(e);
            });
        });
    }

    public async insertrdata(sql){

        return new Promise((resolve, reject) => {
            this.executeSQL(sql).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }

    private isValidEmail(mail) {
        console.log("test valid ", mail);
        return /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test(mail);
        //const re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        // var re = /\S+@\S+\.\S+/;
        // return re.test(mail);
    }

    public selectUltimo() {
        return new Promise((resolve, reject) => {
            let sql = 'SELECT id FROM clientes ORDER BY id DESC LIMIT 1';
            this.executeSQL(sql).then((data: any) => {
                resolve(data.rows.item(0));
            }).catch((e: any) => {
                reject(e);
            });
        });
    }

    public selectTerritorios() {
        return new Promise((resolve, reject) => {
            let sql = 'SELECT rutaterritorisap, rutaterritorisaptext FROM clientes GROUP BY rutaterritorisap';
            this.executeSQL(sql).then((data: any) => {
                let arr = [];
                for (let i = 0; i < data.rows.length; i++)
                    arr.push(data.rows.item(i));
                resolve(arr);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }

    public async EditRegister(d: any, id: any) {

        console.log("data a editar cliente ", d);
        let campos = new Camposusuario();
        let session = await campos.consultasesion();
        let sql2 = await campos.camposusuarioupdate2(d.camposusuario,3);
        
        return new Promise((resolve, reject) => {
            let sql = `UPDATE clientes SET rutaterritorisaptext = '${d.rutaterritorisaptext}', export = 0, FederalTaxId = '${d.FederalTaxId}', PhoneNumber = '${d.PhoneNumber}', comentario = '${d.comentario}', 
                       CardName = '${d.CardName}', correoelectronico = '${d.correoelectronico}', celular = '${d.celular}', Address = '${d.Address}', 
                       pesonacontactocelular = '${d.pesonacontactocelular}', diavisita = '${d.diavisita}', razonsocial = '${d.razonsocial}', tipoEmpresa = '${d.idEmpresa}', img = '${d.img}',
                       cliente_std1 = '${d.cliente_std1}',cliente_std2 = '${d.cliente_std2}',cliente_std3 = '${d.cliente_std3}',cliente_std4 = '${d.cliente_std4}',cliente_std5 = '${d.cliente_std5}',
                       cliente_std6 = '${d.cliente_std6}',cliente_std7 = '${d.cliente_std7}',cliente_std8 = '${d.cliente_std8}',cliente_std9 = '${d.cliente_std9}',cliente_std10 = '${d.cliente_std10}',
                       codeCanal = '${d.codeCanal}',
                  codeSubCanal= '${d.codeSubCanal}',
                  codeTipoTienda= '${d.codeTipoTienda}',
                  cadena= '${d.cadena}',
                  cuccs='${d.cuccs}',
                  Latitude= '${d.Latitude}',
                  Longitude='${d.Longitude}',
                  codeCadenaConsolidador= '${d.codeCadenaConsolidador}',
                  codeCadenaConsolidador= '${d.codeCadenaConsolidador}',Fex_codigoexcepcion= '${d.Fex_codigoexcepcion}',Fex_tipodocumento= '${d.Fex_tipodocumento}',Fex_complemento= '${d.Fex_complemento}',
                  cadenaTxt= '${d.cadenaTxt}',actualizado= 'Y',
                  U_EXX_TIPOPERS='${d.U_EXX_TIPOPERS}',U_EXX_TIPODOCU='${d.U_EXX_TIPODOCU}',U_EXX_APELLPAT='${d.U_EXX_APELLPAT}',
                  U_EXX_APELLMAT='${d.U_EXX_APELLMAT}',U_EXX_PRIMERNO='${d.U_EXX_PRIMERNO}',U_EXX_SEGUNDNO='${d.U_EXX_SEGUNDNO}'
                  `+sql2+`
                       WHERE CardCode = '${d.CardCode}' `;
            // let sql = `UPDATE clientes SET rutaterritorisaptext = '${d.rutaterritorisaptext}', export = 0, FederalTaxId = '${d.FederalTaxId}', PhoneNumber = '${d.PhoneNumber}', comentario = '${d.comentario}', 
            //            CardName = '${d.CardName}', correoelectronico = '${d.correoelectronico}', celular = '${d.celular}', Address = '${d.Address}', 
            //            pesonacontactocelular = '${d.pesonacontactocelular}', diavisita = '${d.diavisita}', tipoEmpresa = '${d.idEmpresa}', img = '${d.img}',
            //            cliente_std1 = '${d.cliente_std1}',cliente_std2 = '${d.cliente_std2}',cliente_std3 = '${d.cliente_std3}',cliente_std4 = '${d.cliente_std4}',cliente_std5 = '${d.cliente_std5}',
            //            cliente_std6 = '${d.cliente_std6}',cliente_std7 = '${d.cliente_std7}',cliente_std8 = '${d.cliente_std8}',cliente_std9 = '${d.cliente_std9}',cliente_std10 = '${d.cliente_std10}',
            //            codeCanal = '${d.codeCanal}',
            //       codeSubCanal= '${d.codeSubCanal}',
            //       codeTipoTienda= '${d.codeTipoTienda}',
            //       cadena= '${d.cadena}',
            //       cuccs='${d.cuccs}',
            //       Latitude= '${d.Latitude}',
            //       Longitude='${d.Longitude}',
            //       codeCadenaConsolidador= '${d.codeCadenaConsolidador}',
            //       codeCadenaConsolidador= '${d.codeCadenaConsolidador}',Fex_codigoexcepcion= '${d.Fex_codigoexcepcion}',Fex_tipodocumento= '${d.Fex_tipodocumento}',Fex_complemento= '${d.Fex_complemento}',
            //       cadenaTxt= '${d.cadenaTxt}',
            //       actualizado= 'Y'
            //       `+sql2+`
            //            WHERE CardCode = '${d.CardCode}' `;
            console.log(sql);
            this.executeSQL(sql).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }
    public async insertRegister(d: any, id: any,estado: any) {
        console.log("objeto a registrar ", d);
        let campos = new Camposusuario();

        let sql2 = await campos.camposusuario(d,3);
        
        return new Promise((resolve, reject) => {
            let sql = `INSERT INTO clientes VALUES (NULL, '${d.img}','${d.idUser}','${d.CardCode}','${d.CardName}','${d.CardType}',
            '${d.Address}','${d.CreditLimit}','${d.MaxCommitment}','${d.DiscountPercent}','${d.PriceListNum}',
            '${d.SalesPersonCode}','${d.Currency}','${d.County}','${d.Country}','${d.CurrentAccountBalance}',
            '${d.NoDiscounts}','${d.PriceMode}','${d.FederalTaxId}','${d.PhoneNumber}','${d.ContactPerson}',
            '${d.PayTermsGrpCode}','${d.Latitude}','${d.Longitude}','${d.GroupCode}','${d.User}','${d.Status}',
            '${d.DateUpdate}',${d.creadopor},'${d.imagen}','${d.export}','${d.celular}',${d.pesonacontactocelular}, 
            '${d.correoelectronico}','${d.rutaterritorisap}','${d.rutaterritorisaptext}','${d.diavisita}', 
            '${d.comentario}','${d.creadopor}','${d.xcodigocliente}','${d.fechaset}','${d.fechaupdate}','${d.razonsocial}',
            '${d.idEmpresa}','${d.cliente_std1}','${d.cliente_std2}','${d.cliente_std3}','${d.cliente_std4}','${d.cliente_std5}',
            '${d.cliente_std6}','${d.cliente_std7}','${d.cliente_std8}','${d.cliente_std9}','${d.cliente_std10}','','','','','','','','','','',
            '${d.codeCanal}','${d.codeSubCanal}','${d.codeTipoTienda}','${d.cadena}', '${d.codeCadenaConsolidador}', '${d.cadenaTxt}','${d.CardCode}',
            '${d.cuccs}', '${d.Fex_tipodocumento}','${d.Fex_complemento}','${d.Fex_codigoexcepcion}','${estado}','${d.territorio}','',
            '${d.U_EXX_TIPOPERS}','${d.U_EXX_TIPODOCU}','${d.U_EXX_APELLPAT}','${d.U_EXX_APELLMAT}','${d.U_EXX_PRIMERNO}','${d.U_EXX_SEGUNDNO}'
            `+sql2+`)`;
            // let sql = `INSERT INTO clientes VALUES (NULL, '${d.img}','${d.idUser}','${d.CardCode}','${d.CardName}','${d.CardType}',
            // '${d.Address}','${d.CreditLimit}','${d.MaxCommitment}','${d.DiscountPercent}','${d.PriceListNum}',
            // '${d.SalesPersonCode}','${d.Currency}','${d.County}','${d.Country}','${d.CurrentAccountBalance}',
            // '${d.NoDiscounts}','${d.PriceMode}','${d.FederalTaxId}','${d.PhoneNumber}','${d.ContactPerson}',
            // '${d.PayTermsGrpCode}','${d.Latitude}','${d.Longitude}','${d.GroupCode}','${d.User}','${d.Status}',
            // '${d.DateUpdate}',${d.creadopor},'${d.imagen}','${d.export}','${d.celular}',${d.pesonacontactocelular}, 
            // '${d.correoelectronico}','${d.rutaterritorisap}','${d.rutaterritorisaptext}','${d.diavisita}', 
            // '${d.comentario}','${d.creadopor}','${d.xcodigocliente}','${d.fechaset}','${d.fechaupdate}',
            // '${d.idEmpresa}','${d.cliente_std1}','${d.cliente_std2}','${d.cliente_std3}','${d.cliente_std4}','${d.cliente_std5}',
            // '${d.cliente_std6}','${d.cliente_std7}','${d.cliente_std8}','${d.cliente_std9}','${d.cliente_std10}','','','','','','','','','','',
            // '${d.codeCanal}','${d.codeSubCanal}','${d.codeTipoTienda}','${d.cadena}', '${d.codeCadenaConsolidador}', '${d.cadenaTxt}','${d.CardCode}',
            // '${d.cuccs}', '${d.Fex_tipodocumento}','${d.Fex_complemento}','${d.Fex_codigoexcepcion}','${estado}','${d.territorio}',''`+sql2+`)`;
            console.log("sql cliente guardar ---->", sql);
            this.executeSQL(sql).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }


    public insert(d: any, id: any) {
        return new Promise((resolve, reject) => {
            let sql = `INSERT INTO clientes VALUES (NULL, '0','${d.creadopor}','${d.CardCode}','${d.CardName}','${d.CardType}','${d.Address}',
            '${d.CreditLimit}','${d.MaxCommitment}','${d.DiscountPercent}','${d.PriceListNum}','${d.SalesPersonCode}',
            '${d.Currency}','${d.County}','${d.Country}','${d.CurrentAccountBalance}','${d.NoDiscounts}','${d.PriceMode}',
            '${d.FederalTaxId}','${d.PhoneNumber}','${d.ContactPerson}','${d.PayTermsGrpCode}','${d.Latitude}',
            '${d.Longitude}','${d.GroupCode}', '${d.User}','${d.Status}','${d.DateUpdate}',${id},'${d.imagen}',
            '100','0','0','0','0','0','0','0','0','0','0','0','0','${d.idEmpresa}','0','Todos','','','','','','','','','','','','','','','','','','','','','','','','','', '', 'Y','')`;
            this.executeSQL(sql).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }

    public selectCarCodeValidate(id: any) {
        return new Promise((resolve, reject) => {
            let sql = 'SELECT count(*) as total FROM clientes WHERE CardCode = "' + id + '"';
            this.executeSQL(sql).then((data: any) => {
                resolve(data.rows.item(0));
            }).catch((e: any) => {
                reject(e);
            });
        });
    }

    public async validate(d: any, x = false) {        
        console.log(" d  ", d);

        if (d.U_EXX_TIPOPERS == "TPN"){
            if(d.U_EXX_APELLPAT == "" || !d.U_EXX_APELLPAT){
                return "Debe llenar el campo apellido paterno"
            }

            if(d.U_EXX_APELLMAT == "" || !d.U_EXX_APELLMAT){
                return "Debe llenar el campo apellido materno"
            }

            if(d.U_EXX_PRIMERNO == "" || !d.U_EXX_PRIMERNO){
                return "Debe llenar el campo primer nombre"
            }
        }

        if (d.CardCode == '')
            return 'Código es un campo requerido.';

        if (d.CardName == '')
            return 'Nombre es un campo requerido.';

        // if (d.razonsocial.trim() == '' || d.razonsocial.trim() == 'SIN NOMBRE')
        //     return 'Razón social es un campo requerido.';
     
        if (d.Address == '')
            return 'Dirección es un campo requerido.';
            
        if (d.FederalTaxId.trim() == '' || d.FederalTaxId.trim() == '0')
            return 'Documento es un campo requerido.';

        // let soloCadena = /^[A-Za-z\s]+$/;
        let soloCadena = /^[A-Za-zñÑ0-9\s]+$/;

        //  console.log("emailRegex.test(d.correoelectronico) ", celularRegex.test(data.contactPhone));
        // if (!soloCadena.test(d.CardName)) {
        //     return 'Nombre no es válido, ingresa letras o numeros.' + d.CardName;
        // }

        // let celularTelefono = /^[0-9]{7,10}/i;

        // if (d.PhoneNumber !== "" && d.PhoneNumber != null && d.PhoneNumber != "null") {
        //     if (!celularTelefono.test(d.PhoneNumber)) {
        //         return 'Teléfono no es válido.';
        //     }
        //     if (d.PhoneNumber.toString().charAt(0) != '2' && d.PhoneNumber.toString().charAt(0) != '3' && d.PhoneNumber.toString().charAt(0) != '4') { return 'Teléfono debe empezar con 2-3-4.'; }
        // }

        // if (d.celular == '')
        // return 'Celular es un campo requerido.';

        // let celularRegex = /^[0-9]{7}/i;
        // if (!celularRegex.test(d.celular))
        //     return 'Celular no es válido.';

        // if (d.celular.toString().charAt(0) != '6' && d.celular.toString().charAt(0) != '7')
        //     return 'Celular debe empezar en 6 - 7';

        
        if (d.correoelectronico == '')
            return 'Email es un campo requerido.';

        let emailRegex = /^[-\w.%+]{1,64}@(?:[A-Z0-9-]{1,63}\.){1,125}[A-Z]{2,63}$/i;
        if (!emailRegex.test(d.correoelectronico))
            return 'Correo electrónico no es válido.';

        const rnit = /^[0-9]*$/;

        let cantCaracteres = 0;
        
        if(d.Fex_tipodocumento == 5){
            cantCaracteres = 11;
        }else if(d.Fex_tipodocumento == 1) {
            cantCaracteres = 8;
        }else{
            cantCaracteres = 20;
        }

        if (d.FederalTaxId !== "" && d.FederalTaxId != null && d.FederalTaxId != 0) {
            if (d.FederalTaxId.length !== cantCaracteres)
                return `Documento no es válido. ${d.FederalTaxId.length} - ${cantCaracteres} - ${d.Fex_tipodocumento}`;

            if (!rnit.test(d.FederalTaxId))
                return 'Documento no es válido.';
        }

        // if (d.razonsocial !== "" && d.razonsocial != null) {
        //     if (d.razonsocial.length < 3)
        //         return 'Razón social no es válido.';
        // }

        if(d.territorio == undefined)  {
            return 'Seleccione el Territorio del Cliente';
        }


        if(d.Address == undefined){
            return 'Ingrese Dirección del Cliente';
        }

        if (d.Address.length < 5){
            return 'Ingrese Dirección valida del Cliente';
        }



        if(d.diavisita == ''){
            return 'Seleccione los días de visita';
        } 


        if (d.SucursalesCliente && d.SucursalesCliente.length == 0) {
            return 'Es necesario que registre al menos una Sucursal de Envio o una de Cobro';
        }

        return 'OK';
    }

    public async validaNIT(nit: any, cardCode) {
        let sql = `SELECT COUNT(*) as total FROM clientes WHERE FederalTaxId != '0' AND FederalTaxId = '${nit}' AND CardCode != '${cardCode}' `;
        console.log(" sql ", sql);
        return await this.queryAll(sql);
    }

    public update(d: any, id: any) {
        return new Promise((resolve, reject) => {
            let sql = `UPDATE clientes SET 
            CardCode = '${d.CardCode}', 
            CardName = '${d.CardName}', 
            CardType = '${d.CardType}', 
            Address = '${d.Address}', 
            CreditLimit = '${d.CreditLimit}',
            MaxCommitment = '${d.MaxCommitment}',
            DiscountPercent = '${d.DiscountPercent}',
            PriceListNum = '${d.PriceListNum}',
            SalesPersonCode = '${d.SalesPersonCode}',
            Currency = '${d.Currency}',
            County = '${d.County}',
            Country = '${d.Country}', 
            CurrentAccountBalance = '${d.CurrentAccountBalance}', 
            NoDiscounts = '${d.NoDiscounts}', 
            PriceMode = '${d.PriceMode}', 
            FederalTaxId = '${d.FederalTaxId}', 
            PhoneNumber = '${d.PhoneNumber}', 
            ContactPerson = '${d.ContactPerson}',
            PayTermsGrpCode = '${d.PayTermsGrpCode}',
            Latitude = '${d.Latitude}',
            Longitude = '${d.Longitude}',
            GroupCode = '${d.GroupCode}',
            User = '${d.User}', Status = '${d.Status}',
            DateUpdate =  '${d.DateUpdate}' WHERE idDocumento = ${id}`;
            this.executeSQL(sql).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }

    public updateLocate(lat: any, lng: any, ubi: string, id: any) {
        return new Promise((resolve, reject) => {
            let sql = `UPDATE clientes SET Address = '${ubi}', Latitude = '${lat}', Longitude = '${lng}', export = 0 WHERE CardCode = '${id}'`;
            this.executeSQL(sql).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }

    public async selectOnline(code: string) {
        let sql = `SELECT * FROM clientes WHERE CardCode = '${code}' `;
        return await this.queryAll(sql);
    }

    public select(id: number) {
        return new Promise((resolve, reject) => {
            let sql = 'SELECT * FROM clientes WHERE id = ' + id;
            this.executeSQL(sql).then((data: any) => {
                resolve(data.rows.item(0));
            }).catch((e: any) => {
                reject(e);
            });
        });
    }

    public selectAll() {
        return new Promise((resolve, reject) => {
            let sql = 'SELECT * FROM clientes';
            this.executeSQL(sql).then((data: any) => {
                resolve(data.rows.item(0));
            }).catch((e: any) => {
                reject(e);
            });
        });
    }


    public selectCarCode(id: any) {
        return new Promise((resolve, reject) => {
            let sql = 'SELECT * FROM clientes WHERE  CardCode = "' + id + '"';
            this.executeSQL(sql).then((data: any) => {
                resolve(data.rows.item(0));
            }).catch((e: any) => {
                reject(e);
            });
        });
    }

    public exportAll() {
        return new Promise((resolve, reject) => {
            let sql = 'SELECT * FROM clientes WHERE export = 0';
            this.executeSQL(sql).then((data: any) => {
                let arr = [];
                for (let i = 0; i < data.rows.length; i++)
                    arr.push(data.rows.item(i));
                resolve(arr);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }

    public importAllImg() {
        return new Promise((resolve, reject) => {
            let sql = 'SELECT img FROM clientes';
            this.executeSQL(sql).then((data: any) => {
                let arr = [];
                for (let i = 0; i < data.rows.length; i++)
                    arr.push(data.rows.item(i));
                resolve(arr);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }

    public exportOne(cardcode: string, cardname: string) {
        return new Promise((resolve, reject) => {
            let sql = `SELECT * FROM clientes WHERE CardCode='` + cardcode + `' AND CardName='` + cardname + `'`;
            this.executeSQL(sql).then((data: any) => {
                let arr = [];
                for (let i = 0; i < data.rows.length; i++)
                    arr.push(data.rows.item(i));
                resolve(arr);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }

    public updateImport(cardcode: string) {
        return new Promise((resolve, reject) => {
            let sql = `UPDATE clientes SET export = 10,actualizado = '' WHERE CardCode = '${cardcode}'`;
            console.log("sql update cliente sincronizado ", sql);
            this.executeSQL(sql).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }

    public updateExport(cardcode: string, cardname: string) {
        return new Promise((resolve, reject) => {
            let sql = `UPDATE clientes SET export = 0 WHERE CardCode = '` + cardcode + `' AND CardName='` + cardname + `'`;
            this.executeSQL(sql).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }

    public async findAllCliente(limit: number, searchData: string, origen: string) {
        let addSql = '';
        let sql = '';
        if (origen == 'all' || origen == 'age') {
            // if (searchData != '') addSql = ` WHERE CardName LIKE '%${searchData}%' OR CardCode LIKE '%${searchData}%' OR FederalTaxId LIKE '%${searchData}%' OR razonsocial LIKE '%${searchData}%'  `;
            if (searchData != '') addSql = ` WHERE CardName LIKE '%${searchData}%' OR CardCode LIKE '%${searchData}%' OR FederalTaxId LIKE '%${searchData}%' `;
            else addSql = '';
            sql = 'SELECT * FROM clientes ' + addSql + ' GROUP BY CardCode ORDER BY CardName ASC LIMIT ' + limit + ', 20';
            console.log(sql);
            return this.queryAll(sql);
        }
    }

    public findAll(limit: number, searchData: string, origen: string) {
        return new Promise((resolve, reject) => {
            let addSql = '';
            let sql = '';
            if (origen == 'all' || origen == 'age') {
                (searchData != '') ? addSql = ' WHERE CardName LIKE "%' + searchData + '%"  OR CardCode LIKE "%' + searchData + '%"  OR FederalTaxId LIKE  "%' + searchData + '%" ' : addSql = '';
                sql = 'SELECT * FROM clientes ' + addSql + ' ORDER BY CardName ASC LIMIT ' + limit + ', 20';
            } else {
                let insql = '(SELECT id FROM documentos d WHERE d.CardCode = c.CardCode AND d.DocType = "DFA" AND d.tipoestado != "null")';
                let totalsql = '(SELECT (SUM(icett)) - (SELECT SUM(d.descuento) FROM documentos d WHERE d.CardCode = c.CardCode AND d.DocType = "DFA") FROM detalle WHERE idDocumento IN ' + insql + ') - CASE WHEN (SELECT SUM(monto) FROM pagos p WHERE  documentoId IN ' + insql + ') IS NULL THEN 0 ELSE (SELECT SUM(monto) FROM pagos p WHERE  documentoId IN ' + insql + ') END';
                (searchData != '') ? addSql = ' AND CardName LIKE "%' + searchData + '%"  OR CardCode LIKE "%' + searchData + '%"  OR FederalTaxId LIKE  "%' + searchData + '%" ' : addSql = '';
                let sqlx = '(SELECT count(*) FROM documentos d WHERE d.CardCode = c.CardCode AND d.DocType = "DFA" AND d.tipoestado != "null" AND (SELECT SUM(icett) FROM detalle WHERE idDocumento = d.id) > CASE WHEN (SELECT SUM(monto) FROM pagos p WHERE p.documentoId = d.id) IS NULL THEN 0 ELSE (SELECT SUM(monto) FROM pagos p WHERE p.documentoId = d.id) END)';
                sql = 'SELECT c.*, ' +
                    'printf("%.2f",' + totalsql + ') totalNeto ' +
                    'FROM clientes c WHERE ' + sqlx + ' > 0 ' + addSql + ' GROUP BY c.CardCode ORDER BY c.CardName ASC LIMIT ' + limit + ', 20';
            }
            this.executeSQL(sql).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }

    public async findSearch(dato: string, tipo = '') {
        let rx = ` `;
        (tipo != '') ? rx = ` c.cliente_std1 IN(${tipo}) AND ` : rx = ` `;
        // let sql = `SELECT * FROM clientes c WHERE${rx}(c.CardName LIKE '%${dato}%' OR c.CardCode LIKE '%${dato}%' OR c.razonsocial LIKE '%${dato}%' OR c.FederalTaxId LIKE '%${dato}%')  LIMIT 25`;
        let sql = `SELECT * FROM clientes c WHERE${rx}(c.CardName LIKE '%${dato}%' OR c.CardCode LIKE '%${dato}%' OR c.FederalTaxId LIKE '%${dato}%')  LIMIT 25`;
        console.log("sql query ", sql);
        return await this.queryAll(sql);
    }

    public async selectClientesPos() {
        let sql = 'SELECT c.Latitude, c.Longitude, c.* FROM clientes c where (c.Latitude<>"0" or c.Latitude<>"Null" or c.Latitude<>"")  ORDER BY id DESC LIMIT 30 ';
        return await this.queryAll(sql);
    }

    public async selectBuscarClientesPos(dato) {
        let sql = 'SELECT c.Latitude, c.Longitude, c.* FROM clientes c where (c.Latitude<>"0" or c.Latitude<>"Null" or c.Latitude<>"") and (c.CardName LIKE "%' + dato +
            '%" OR c.CardCode LIKE  "%' + dato +
            '%" OR c.FederalTaxId LIKE "%' + dato +
            '%")  ORDER BY id DESC LIMIT 10 ';
        return await this.queryAll(sql);
    }

    public drop() {
        return new Promise((resolve, reject) => {
            let sql = `DROP TABLE IF EXISTS clientes;`;
            this.executeSQL(sql).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }

    public updateDataSapClient(client) {
        
        return new Promise((resolve, reject) => {
            let sql = `UPDATE clientes SET CurrentAccountBalance = '${client[0].CurrentAccountBalance}', CreditLimit = '${client[0].CreditLimit}' WHERE CardCode = '${client[0].CardCode}'`;
           console.log("sql update cliente sincronizado ", sql);
            this.executeSQL(sql).then((data: any) => {
                resolve(data);
            }).catch((e: any) => {
                reject(e);
            });
        });
    }




}
