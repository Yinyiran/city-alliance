import { Injectable, Logger } from '@nestjs/common';

export interface PrintReceiptParams {
  shopName: string;
  storeName: string;
  orderNo: string;
  tradeType: string;
  createdAt: string;
  items: {
    name: string;
    quantity: number;
    price: string;
  }[];
  totalAmount: string;
  payAmount: string;
  verifyCode?: string | null;
  printerSn?: string | null;
  printerType?: string | null; // YILIANYUN, FEIE
}

@Injectable()
export class PrinterService {
  private readonly logger = new Logger(PrinterService.name);

  /**
   * 格式化标准 58mm 热敏云小票内容
   */
  formatReceiptContent(params: PrintReceiptParams): string {
    const divider = '--------------------------------\n';
    let content = '';
    content += `<FS2><center># 城盟优选 #</center></FS2>\n`;
    content += `<center>** ${params.shopName} **</center>\n`;
    content += `<center>${params.storeName}</center>\n`;
    content += divider;
    content += `订单编号: ${params.orderNo}\n`;
    content += `履约类型: ${params.tradeType}\n`;
    content += `下单时间: ${params.createdAt}\n`;
    content += divider;
    content += `商品名称            数量  金额\n`;

    for (const item of params.items) {
      const name = item.name.padEnd(10, ' ').slice(0, 10);
      const qty = String(item.quantity).padStart(4, ' ');
      const price = item.price.padStart(8, ' ');
      content += `${name} ${qty} ${price}\n`;
    }

    content += divider;
    content += `订单总额: ￥${params.totalAmount}\n`;
    content += `<FS><right>实付金额: ￥${params.payAmount}</right></FS>\n`;

    if (params.verifyCode) {
      content += divider;
      content += `<FS2><center>核销券码: ${params.verifyCode}</center></FS2>\n`;
      content += `<center><QR>${params.verifyCode}</QR></center>\n`;
    }

    content += divider;
    content += `<center>感谢光临，欢迎再次惠顾！</center>\n\n\n`;

    return content;
  }

  /**
   * 即时触发云小票打印
   */
  async printOrderReceipt(params: PrintReceiptParams): Promise<boolean> {
    const receiptText = this.formatReceiptContent(params);
    this.logger.log(`[云打印机出票] 设备 SN: ${params.printerSn || 'MOCK_PRINTER'}`);
    this.logger.debug(`打印报文:\n${receiptText}`);

    // 本地及测试环境下，模拟打印成功
    return true;
  }
}
