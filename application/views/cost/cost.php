
<!--หน้าต่างแสดงค่าใช้ที่เดิดขึ้นของผู้ใช้งาน ที่จุดข้ายตั๋ว-->
<script>
    jQuery(document).ready(function ($) {
        $("#mainmenu ul li").removeAttr('class');
        $("#btnCost").addClass("active");
    });
</script>
<div class="container" style="">
    <div class="row">        
        <div class="page-header">        
            <h3>
                <?php echo $page_title; ?>                
                <font color="#777777">
                <span style="font-size: 23px; line-height: 23.399999618530273px;">&nbsp;&nbsp;<?php echo $page_title_small; ?></span>                
                </font>
            </h3>        
        </div>
    </div>
</div>
<div class="container">  
    <div class="row">
        <div class="col-lg-12 col-md-12">
            <div class="widget">
                <div class="widget-header">
                    <i class="fa fa-search"></i>
                    <span>ค้นหา</span>
                </div>
                <div class="widget-content">
                    <div class="col-md-8 col-md-offset-2">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">เส้นทาง</label>
                                <input type="email" class="form-control" id="" placeholder="">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">วันที่</label>
                                <input type="email" class="form-control" id="" placeholder="">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">จุดจำหน่ายตั๋ว</label>
                                <input type="email" class="form-control" id="" placeholder="">
                            </div>
                        </div>
                        <div class="col-md-12 text-center">
                            <button type="submit" class="btn btn-default">ค้นหา</button>
                        </div>
                    </div>
                </div>
            </div>  
            <div class="widget">
                <div class="col-md-12 text-right">   
                    <?php
                    $income = array(
                        'type' => "button",
                        'class' => "btn btn-info btn-lg",
                    );
                    $outcome = array(
                        'type' => "button",
                        'class' => "btn btn-warning btn-lg",
                    );
                    echo anchor('cost/add/1', '<span class="fa fa-plus">&nbsp;&nbsp;รายรับ</span>', $income) . '  ';
                    echo anchor('cost/add/2', '<span class="fa fa-minus">&nbsp;&nbsp;รายจ่าย</span>', $outcome);
                    ?> 
                </div>  
            </div>
        </div>

    </div>
    <div class="row">
        <div class="col-md-12" style="" >
            <div class="widget-content">
                <div id="big_stats" class="cf">
                    <div class="stat"> 
                        <i class="fa">รายรับ</i>
                        <span class="value">851</span> 
                    </div>
                    <div class="stat"> 
                        <i class="fa">รายจ่าย</i>
                        <span class="value">851</span> 
                    </div>  
                    <div class="stat"> 
                        <i class="fa">คงเหลือ</i>
                        <span class="value">851</span> 
                    </div>
                </div>            
            </div>
        </div>
        <div class="row">
            <?php
            foreach ($cost_types as $cost_type) {
                $CostTypeID = $cost_type['CostTypeID'];
                $CostTypeName = $cost_type['CostTypeName'];
                ?>
                <div class="col-lg-6 col-md-6" style="padding-top: 1%;">
                    <div class="widget widget-table action-table">
                        <div class="widget-header">
                            <span class=""><?= $CostTypeName ?></span>
                        </div>                
                        <div class="widget-content"> 
                            12346464
                            <div class="col-lg-12">
                                <table class="overflow-y">
                                    <thead>
                                        <tr>
                                            <th style="width: 10%">ลำดับ</th>                                   
                                            <th style="width: 70%;">รายการ</th>
                                            <th style="width: 20%">จำนวนเงิน</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        //เเสดงค่าใช้จ่ายที่เกิดขึ้นโดยมีคนป้อนข้อมูล เช่นค่าน้ำมัน ค่ารายทาง
//                                        foreach ($costs as $cost) {
                                            for($i=0;$i<25;$i++){
                                            ?>
                                            <tr>
                                                <td class="text-center"><?= 5 ?></td>                                       
                                                <td>dgsdfgsfgfsdgsfgsdfg</td> 
                                                <td>sgdsgsgsfgsfg</td> 
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                    </tbody>                            
                                </table>  
                            </div>
                        </div>                
                    </div>
                </div>  
            <?php } ?>
        </div>
    </div> 
</div>
///
<div class="container-fluid hidden">     
    <table class="overflow-y">
        <thead>
            <tr>
                <th>Population</th><th>Alpha</th><th>Beta</th><th>Gamma</th><th>Delta</th><th>Epsilon</th><th>Zeta</th><th>Eta</th><th>Theta</th><th>Iota</th><th>Kappa</th><th>Lambda</th><th>Mu</th><th>Nu</th><th>Xi</th><th>Omicron</th><th>Pi</th><th>Rho</th><th>Sigma</th><th>Tau</th><th>Upsilon</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <th>Sample #1</th><td>23</td><td>88</td><td>8</td><td>2</td><td>67</td><td>83</td><td>81</td><td>37</td><td>91</td><td>96</td><td>13</td><td>3</td><td>95</td><td>98</td><td>10</td><td>87</td><td>70</td><td>54</td><td>72</td><td>75</td>
            </tr><tr>
                <th>Sample #2</th><td>14</td><td>21</td><td>20</td><td>21</td><td>9</td><td>68</td><td>60</td><td>73</td><td>22</td><td>29</td><td>9</td><td>49</td><td>49</td><td>66</td><td>58</td><td>10</td><td>8</td><td>24</td><td>19</td><td>65</td>
            </tr><tr>
                <th>Sample #3</th><td>4</td><td>50</td><td>89</td><td>72</td><td>99</td><td>90</td><td>24</td><td>86</td><td>95</td><td>44</td><td>32</td><td>97</td><td>18</td><td>90</td><td>81</td><td>9</td><td>38</td><td>4</td><td>85</td><td>15</td>
            </tr><tr>
                <th>Sample #4</th><td>10</td><td class="err">Parse error</td><td>32</td><td>45</td><td>53</td><td>29</td><td>35</td><td>35</td><td>75</td><td>9</td><td>69</td><td>66</td><td>93</td><td>42</td><td>81</td><td>85</td><td>72</td><td>70</td><td>15</td><td>38</td>
            </tr><tr>
                <th>Sample #5</th><td>85</td><td>42</td><td>71</td><td>56</td><td>30</td><td>3</td><td>41</td><td>87</td><td>94</td><td>99</td><td>24</td><td>20</td><td>96</td><td>62</td><td>90</td><td>13</td><td>38</td><td>47</td><td>9</td><td>6</td>
            </tr><tr>
                <th>Sample #6</th><td>18</td><td>80</td><td>85</td><td>65</td><td>9</td><td>93</td><td>93</td><td>61</td><td>49</td><td>10</td><td>45</td><td>3</td><td>93</td><td>61</td><td>4</td><td>80</td><td>2</td><td>60</td><td>53</td><td>81</td>
            </tr><tr>
                <th>Sample #7</th><td>30</td><td>81</td><td>46</td><td>50</td><td>71</td><td>60</td><td>8</td><td>33</td><td>87</td><td>34</td><td>35</td><td>74</td><td>34</td><td>31</td><td>97</td><td>10</td><td>40</td><td>95</td><td>92</td><td>93</td>
            </tr><tr>
                <th>Sample #8</th><td>29</td><td>91</td><td>85</td><td>92</td><td>2</td><td>84</td><td>29</td><td>28</td><td>25</td><td>63</td><td>18</td><td>97</td><td>87</td><td>59</td><td>53</td><td>7</td><td>47</td><td>21</td><td>62</td><td>11</td>
            </tr><tr>
                <th>Sample #9</th><td>45</td><td>96</td><td>25</td><td>60</td><td>56</td><td>67</td><td>48</td><td>7</td><td>30</td><td>64</td><td>10</td><td>0</td><td>38</td><td>72</td><td>83</td><td>61</td><td>35</td><td>96</td><td>84</td><td>49</td>
            </tr><tr>
                <th>Sample #10</th><td>66</td><td>63</td><td>25</td><td>28</td><td>67</td><td>83</td><td>25</td><td>10</td><td>0</td><td>18</td><td>98</td><td>92</td><td>73</td><td>40</td><td>78</td><td>88</td><td>99</td><td>30</td><td>74</td><td>88</td>
            </tr><tr>
                <th>Sample #11</th><td>8</td><td>34</td><td>9</td><td>56</td><td>38</td><td>37</td><td>17</td><td>74</td><td>33</td><td>55</td><td>76</td><td>95</td><td>34</td><td>5</td><td>39</td><td>13</td><td>99</td><td>35</td><td>15</td><td>56</td>
            </tr><tr>
                <th>Sample #12</th><td>28</td><td>1</td><td>93</td><td>79</td><td>56</td><td>7</td><td>70</td><td>62</td><td>58</td><td>96</td><td>25</td><td>40</td><td>49</td><td>35</td><td>44</td><td>67</td><td>6</td><td>73</td><td>38</td><td>91</td>
            </tr><tr>
                <th>Sample #13</th><td>85</td><td>1</td><td>70</td><td>31</td><td>32</td><td>42</td><td>91</td><td>75</td><td>51</td><td>77</td><td>35</td><td>53</td><td>7</td><td>79</td><td>17</td><td>75</td><td>55</td><td>47</td><td>42</td><td>41</td>
            </tr><tr>
                <th>Sample #14</th><td>93</td><td>59</td><td>47</td><td>68</td><td>75</td><td>61</td><td>37</td><td>34</td><td>44</td><td>36</td><td>59</td><td>95</td><td>31</td><td>10</td><td>11</td><td>62</td><td>98</td><td>34</td><td>58</td><td>93</td>
            </tr><tr>
                <th>Sample #15</th><td>81</td><td>28</td><td>36</td><td>88</td><td>85</td><td>66</td><td>66</td><td>68</td><td>78</td><td>64</td><td>95</td><td>59</td><td>35</td><td>15</td><td>51</td><td>84</td><td>59</td><td>29</td><td>22</td><td>35</td>
            </tr><tr>
                <th>Sample #16</th><td>71</td><td>90</td><td>78</td><td>60</td><td>28</td><td>61</td><td>88</td><td>2</td><td>23</td><td>48</td><td>11</td><td>79</td><td>93</td><td>19</td><td>74</td><td>31</td><td>55</td><td>10</td><td>70</td><td>95</td>
            </tr><tr>
                <th>Sample #17</th><td>64</td><td>17</td><td>49</td><td>71</td><td>6</td><td>44</td><td>38</td><td>14</td><td>95</td><td>70</td><td>69</td><td>9</td><td>76</td><td>41</td><td>77</td><td>83</td><td>99</td><td>43</td><td>54</td><td>33</td>
            </tr><tr>
                <th>Sample #18</th><td>20</td><td>36</td><td>10</td><td>0</td><td>35</td><td>35</td><td>2</td><td>29</td><td>98</td><td>22</td><td>30</td><td>45</td><td>49</td><td>80</td><td>48</td><td>20</td><td>11</td><td>31</td><td>14</td><td>12</td>
            </tr><tr>
                <th>Sample #19</th><td>23</td><td>74</td><td>72</td><td>43</td><td>99</td><td class="err">Parse error</td><td>96</td><td>34</td><td>9</td><td>59</td><td>56</td><td>10</td><td>19</td><td>53</td><td>21</td><td>71</td><td>75</td><td>55</td><td>51</td><td>82</td>
            </tr><tr>
                <th>Sample #20</th><td>16</td><td>88</td><td>17</td><td>85</td><td>6</td><td>45</td><td>41</td><td>67</td><td>12</td><td>70</td><td>83</td><td>73</td><td>85</td><td>19</td><td>4</td><td>5</td><td>13</td><td>85</td><td>53</td><td>6</td>
            </tr><tr>
                <th>Sample #21</th><td>35</td><td>34</td><td>69</td><td>78</td><td>10</td><td>89</td><td>38</td><td>81</td><td>95</td><td>51</td><td>37</td><td>49</td><td>50</td><td>66</td><td>17</td><td>15</td><td>99</td><td>19</td><td>54</td><td>29</td>
            </tr><tr>
                <th>Sample #22</th><td>88</td><td>65</td><td>97</td><td>73</td><td>38</td><td>74</td><td>92</td><td>86</td><td>75</td><td>77</td><td>34</td><td>28</td><td>31</td><td>12</td><td>78</td><td>25</td><td>79</td><td>60</td><td>8</td><td>86</td>
            </tr><tr>
                <th>Sample #23</th><td>86</td><td>18</td><td>11</td><td>37</td><td>70</td><td>86</td><td>2</td><td>6</td><td>50</td><td>24</td><td>82</td><td>9</td><td>15</td><td>70</td><td>29</td><td>74</td><td>15</td><td>86</td><td>42</td><td>14</td>
            </tr><tr>
                <th>Sample #24</th><td>80</td><td>62</td><td>69</td><td>25</td><td>90</td><td>16</td><td>27</td><td>93</td><td>70</td><td>53</td><td>89</td><td>60</td><td>39</td><td>31</td><td>43</td><td>67</td><td>94</td><td>31</td><td>38</td><td>91</td>
            </tr><tr>
                <th>Sample #25</th><td>94</td><td>80</td><td>13</td><td>11</td><td>65</td><td>20</td><td>85</td><td>86</td><td>51</td><td>67</td><td>15</td><td>54</td><td>34</td><td>75</td><td>87</td><td>79</td><td>11</td><td>43</td><td>32</td><td>52</td>
            </tr>
        </tbody>
    </table>        
</div> 

