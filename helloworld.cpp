#include <stdio.h>
int main()
{
    int jb,mb,db,js,ms,ds, total, detiksa, detikbe, ja, ma, da;
    printf("jam berangkat = "); scanf("%d",&jb);
    printf("menit berangkat = "); scanf("%d",&mb);
    printf("detik berangkat = "); scanf("%d",&db);
    printf("jam sampai = "); scanf("%d",&js);
    printf("menit sampai = "); scanf("%d",&ms);
    printf("detik sampai = "); scanf("%d",&ds); 
    
    jb = jb*3600;
    mb = mb*60;
    db = db;
    detiksa = jb+mb+db;
    
    js = js*3600;
    ms = ms*60;
    ds = ds;
    detikbe = js+ms+ds;
    
    total = detikbe - detiksa;
    
    ja = total/3600;
    total = total%3600;
    ma = total/60;
    da = total%60;
    
    printf(" Lama perjalanan = ");
    printf("%d jam ",ja);
    printf("%d menit ",ma);
    printf("%d detik",da);
    
}