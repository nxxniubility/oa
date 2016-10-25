/*
 ���ݡ��л����񹲺͹����ұ�׼ GB 11643-1999�����йع������ݺ���Ĺ涨���������ݺ�������������룬��ʮ��λ���ֱ������һλ����У������ɡ�����˳�������������Ϊ����λ���ֵ�ַ�룬��λ���ֳ��������룬��λ����˳�����һλ����У���롣
 ��ַ���ʾ�������ס����������(�С��졢��)�������������롣
 �����������ʾ�������������ꡢ�¡��գ������������λ���ֱ�ʾ���ꡢ�¡���֮�䲻�÷ָ�����
 ˳�����ʾͬһ��ַ������ʶ������Χ�ڣ���ͬ�ꡢ�¡��ճ�������Ա�ඨ��˳��š�˳����������ָ����ԣ�ż���ָ�Ů�ԡ�
 У�����Ǹ���ǰ��ʮ��λ�����룬����ISO 7064:1983.MOD 11-2У�����������ļ����롣

 �������ڼ��㷽����
 15λ������֤�������Ȱѳ�������չΪ4λ���򵥵ľ�������һ��19��18,�����Ͱ���������1800-1999���������;
 2000�������Ŀ϶�����18λ����û��������գ�����1800��ǰ������,��ɶ��ʱӦ�û�û����֤������������ѩn��b��...
 �������������ʽ:
 ��������1800-2099  (18|19|20)?\d{2}(0[1-9]|1[12])(0[1-9]|[12]\d|3[01])
 ����֤�������ʽ /^\d{6}(18|19|20)?\d{2}(0[1-9]|1[12])(0[1-9]|[12]\d|3[01])\d{3}(\d|X)$/i
 15λУ����� 6λ��ַ����+6λ��������+3λ˳���
 18λУ����� 6λ��ַ����+8λ��������+3λ˳���+1λУ��λ

 У��λ����     ��ʽ:��(ai��Wi)(mod 11)����������������������������(1)
 ��ʽ(1)�У�
 i----��ʾ�����ַ������������У�������ڵ�λ����ţ�
 ai----��ʾ��iλ���ϵĺ����ַ�ֵ��
 Wi----ʾ��iλ���ϵļ�Ȩ���ӣ�����ֵ���ݹ�ʽWi=2^(n-1��(mod 11)����ó���
 i 18 17 16 15 14 13 12 11 10 9 8 7 6 5 4 3 2 1
 Wi 7 9 10 5 8 4 2 1 6 3 7 9 10 5 8 4 2 1

 */
//����֤�źϷ�����֤
//֧��15λ��18λ����֤��
//֧�ֵ�ַ���롢�������ڡ�У��λ��֤

function checkCodeValid(code) {
    var city={11:"����",12:"���",13:"�ӱ�",14:"ɽ��",15:"���ɹ�",21:"����",22:"����",23:"������ ",31:"�Ϻ�",32:"����",33:"�㽭",34:"����",35:"����",36:"����",37:"ɽ��",41:"����",42:"���� ",43:"����",44:"�㶫",45:"����",46:"����",50:"����",51:"�Ĵ�",52:"����",53:"����",54:"���� ",61:"����",62:"����",63:"�ຣ",64:"����",65:"�½�",71:"̨��",81:"���",82:"����",91:"���� "};
    var tip = "";
    var pass= true;

    if(!code || !/^\d{6}(18|19|20)?\d{2}(0[1-9]|1[12])(0[1-9]|[12]\d|3[01])\d{3}(\d|X)$/i.test(code)){
        tip = "����֤�Ÿ�ʽ����";
        pass = false;
    }

    else if(!city[code.substr(0,2)]){
        tip = "��ַ�������";
        pass = false;
    }
    else{
        //18λ����֤��Ҫ��֤���һλУ��λ
        if(code.length == 18){
            code = code.split('');
            //��(ai��Wi)(mod 11)
            //��Ȩ����
            var factor = [ 7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2 ];
            //У��λ
            var parity = [ 1, 0, 'X', 9, 8, 7, 6, 5, 4, 3, 2 ];
            var sum = 0;
            var ai = 0;
            var wi = 0;
            for (var i = 0; i < 17; i++)
            {
                ai = code[i];
                wi = factor[i];
                sum += ai * wi;
            }
            var last = parity[sum % 11];
            if(parity[sum % 11] != code[17]){
                tip = "У��λ����";
                pass =false;
            }
        }
    }
    //if(!pass) alert(tip);
    return pass;
}


function checkMobile(mobile) {
    var ismobile = /^1[3|4|5|7|8|9]{1}[0-9]{9}$/;
    if(ismobile.test(mobile)){
        return mobile;
    }
    return false;
}