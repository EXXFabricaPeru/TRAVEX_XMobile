<div id="print" class="hidden-print">
    <v-row>
        <v-col cols="12" md="12">
            <v-card>
                <v-card-text>
                    <v-row>
                        <table width="90%" align="center" cellpadding="0" cellspacing="0" class="titulos_detalle2 espacio">
                            <tbody>
                                <tr>
                                    <td colspan="3" align="center">CardName</td>
                                </tr>
                                <tr>
                                    <td colspan="3" align="center"></td>
                                </tr>
                                <tr>
                                    <td colspan="3" align="center">Address</td>
                                </tr>
                                <tr>
                                    <td colspan="3" align="center">TELEFONO: PhoneNumber  </td>
                                </tr>
                                <tr>
                                    <td colspan="3" align="center">Address &nbsp; - Country</td>
                                </tr>
                                <tr>
                                    <td colspan="3" align="center"><strong>
                                            <div v-if="typeOfDocument == 'DFA'">
                                                FACTURA
                                            </div>
                                            <div v-if="typeOfDocument == 'DOF'">
                                                OFERTA
                                            </div>
                                            <div v-if="typeOfDocument == 'DOP'">
                                                PEDIDO
                                            </div>
                                        </strong></td>
                                </tr>
                                <tr>
                                    <td colspan="3" align="center">ACTIVIDAD ECONOMICA</td>
                                </tr>
                                <tr>
                                    <td colspan="3" align="center"> razon social</td>
                                </tr>
                                <tr>
                                    <td colspan="3" align="center">
                                        <hr/>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="49%" class="titulos_detalle2">NIT :</td>
                                    <td colspan="2" align="center" class="titulos_detalle2"><div v-if="typeOfDocument == 'DFA'">FederalTaxId</div></td>
                                </tr>
                                <tr>
                                    <td class="titulos_detalle2">FACTURA NRO :</td>
                                    <td colspan="2" align="center" class="titulos_detalle2"> <div v-if="typeOfDocument == 'DFA'">FederalTaxId</div></td>
                                </tr>
                                <tr>
                                    <td class="titulos_detalle2">AUTORIZACION NRO :</td>
                                    <td colspan="2" align="center" class="titulos_detalle2"><div v-if="typeOfDocument == 'DFA'">@{{ this.lbcc.U_NumeroAutorizacion }}</div></td>
                                </tr>
                                <tr>
                                    <td colspan="3" align="center">
                                        <hr style="border: 1px dashed;" />
                                    </td>
                                </tr>
                                <tr>
                                    <td class="titulos_detalle2">FECHA:</td>
                                    <td colspan="2" align="center" class="titulos_detalle2">&nbsp; DocDueDate</td>
                                </tr>
                                <tr>
                                    <td class="titulos_detalle2">NOMBRE:</td>
                                    <td colspan="2" align="center" class="titulos_detalle2"> CardName</td>
                                </tr>
                                <tr>
                                    <td class="titulos_detalle2">NIT/CI:&nbsp;</td>
                                    <td colspan="2" align="center" class="titulos_detalle2">FederalTaxId</td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td width="18%"></td>
                                    <td width="33%"></td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td colspan="3">
                                        <table class="table table-light " style="width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th width="21%" align="center" class="titulos_detalle"><strong>Cantidad</strong></th>
                                                    <th align="center" class="titulos_detalle"><strong>Producto</strong></th>
                                                    <th width="18%" align="center" class="titulos_detalle"><strong>SubTotal</strong></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td colspan="3">
                                                        <table style="width: 100%;">
                                                            <tbody>
                                                                <tr>
                                                                    <td class="titulos_detalle">@{{ item.Quantity }} -&nbsp; @{{ item.UoMCode }} -&nbsp; @{{ item.Name }}</td>
                                                                    <td class=" titulos_detalle2 text-right"> @{{ item.Total }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="contenido_detalle"><small>PRECIO: @{{ item.Price }} - BRUTO @{{ item.PriceReal }} - DESC: @{{ item.Discount }}
                                                                            ICE:@{{ item.Discount }}</small></td>
                                                                    <td>&nbsp;</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                </tr>
                                                <tr v-for="gifCard in gifCards">
                                                    <td colspan="3">
                                                        <table style="width: 100%;">
                                                            <tbody>
                                                                <tr>
                                                                    <td class="titulos_detalle">@{{ gifCard.Quantity }} -&nbsp; @{{ gifCard.UoMCode }} -&nbsp; @{{ gifCard.Name }}
                                                                    </td>
                                                                    <td class="titulos_detalle2 text-right"> @{{ gifCard.Price }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="contenido_detalle"><small>PRECIO: @{{ gifCard.Price }} - BRUTO @{{ gifCard.PriceReal }} - DESC: @{{ gifCard.Discount }}
                                                                            ICE:@{{ gifCard.Discount }}</small></td>
                                                                    <td>&nbsp;</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                </tr>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="3"></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3"></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3"></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3">
                                                        <hr/>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2" class="contenido_detalle ">TOTAL BRUTO BS</td>
                                                    <td align="right" class="titulos_detalle2 text-right">@{{ header.Total }}</td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2" class="contenido_detalle ">DESCUENTO ( Monto) BS</td>
                                                    <td align="right">@{{ header.discountMonetary }}</td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2" class="contenido_detalle ">TOTAL&nbsp; BS</td>
                                                    <td align="right" class="titulos_detalle2 text-right">@{{ header.TotalPay }}</td>
                                                </tr>
                                                <tr>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3" class="contenido_detalle ">SON: @{{ literal }} 00/100</td>
                                                </tr>
                                                <tr>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3" class="contenido_detalle ">
                                                        <div v-if="typeOfDocument == 'DFA'">CODIGO DE CONTROL:&nbsp; <span class="text-center"> @{{ this.codigocontrol }} </span></div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3" class="contenido_detalle ">
                                                        <div v-if="typeOfDocument == 'DFA'">FECHA LIMITE DE EMISION:&nbsp;</div>
                                                        <div v-else>FECHA DE DOCUMENTO:</div> @{{ this.lbcc.U_FechaLimiteEmision }}
                                                    </td>
                                                </tr>

                                        </table>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <p>&nbsp;</p>
                        <p>&nbsp;</p>
                        <table width="90%" align="center" cellpadding="0" cellspacing="0" class="titulos_detalle2 espacio al_pie">
                            <tbody>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="contenido_detalle ">
                                        <div v-if="typeOfDocument == 'DFA'">FECHA LIMITE DE EMISION:&nbsp;</div>
                                        <div v-else>FECHA DE DOCUMENTO:</div>
                                        @{{ this.lbcc.U_FechaLimiteEmision }} </td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td width="61%" rowspan="6" align="center">
                                        <div v-if="typeOfDocument == 'DFA'"><strong>Codigo QR</strong></div>
                                    </td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td width="61%" rowspan="6" align="center">
                                        <div v-if="typeOfDocument == 'DFA'" id="qrcode" class="box"></div>
                                    </td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td class="contenido_detalle2 "><strong>OBSERVACION:</strong> @{{ header.Commentary }}</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="contenido_detalle2 "><strong>USUARIO:&nbsp;</strong>Tato Pantoja Duran</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="contenido_detalle2 "><strong>CONDICION:&nbsp;</strong>@{{ payTermSelected }} </td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="contenido_detalle2 "><strong>FECHA DE ENTREGA:</strong>&nbsp;@{{ date4 }} </td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="contenido_detalle2 "><strong>MONTO DEUDA&nbsp;:*</strong> @{{ parseInt(header.CurrentAccountBalance) + parseInt(header.TotalPay) }} BS </td>
                                </tr>
                                <tr>
                                    <td><small>*Dato sujeto a verificación</small></td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                </tr>
                            </tfoot>
                        </table>
                        <p>&nbsp;</p>
                        </td>
                        </tr>
                        </table>
                    </v-row>
                </v-card-text>
            </v-card>
        </v-col>
    </v-row>
    <pre>
        <?php
        print_r($documento);
        ?>    
    </pre>


</div>